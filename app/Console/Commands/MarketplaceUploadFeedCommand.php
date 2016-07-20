<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp;

class MarketplaceUploadFeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketplace:feed:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads feeds to marketplaces via FTP, etc.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Retrieve FTP marketplaces
        $marketplaces = \App\Models\Marketplace::enabled()->ftp()->get();

        foreach ($marketplaces as $marketplace) {
            $this->info($marketplace->name);

            // Retrieve sites
            $sites = $marketplace->sites()->wherePivot('marketplace_enabled', 1)->get();
            if (empty($sites)) continue;

            $feeds = [];
            foreach ($sites as $site) {
                $this->line($site->subdomain);

                // Generate the feeds
                $helper = new \App\Models\Site\MarketplaceHelper($site);
                $helper->setMarketplace($marketplace);
                $helper->getMarketplaceXmlProperties();

                // Add to the feeds list
                $remote = $helper->getMarketplaceAdm()->getFeedRemoteFilename($site);
                $feeds[$remote] = $helper->getXmlFilePath('properties');
            }

            $ftp_config = [
                'host' => $marketplace->configuration['ftp']['host'],
                'username' => $marketplace->configuration['ftp']['username'],
                'password' => $marketplace->configuration['ftp']['password'],
                'port' => !empty($marketplace->configuration['ftp']['port']) ? $marketplace->configuration['ftp']['port'] : 21,
                'root' => $marketplace->configuration['ftp']['root'],
                'passive' => !empty($marketplace->configuration['ftp']['passive']),
                'ssl' => !empty($marketplace->configuration['ftp']['ssl']),
                'timeout' => !empty($marketplace->configuration['ftp']['timeout']) ? $marketplace->configuration['ftp']['timeout'] : 20,
            ];

            // If we have feeds, we upload them
            $filesystem = new Filesystem(new Ftp($ftp_config));

            // Upload the files
            foreach ($feeds as $remote => $local) {
                $this->line($local.' > '.$remote);

                $stream = fopen($local, 'r+');
                try {
                    $filesystem->writeStream($remote, $stream);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                fclose($stream);
            }
        }
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @param  string  $style
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function line($string, $style = null, $verbosity = null)
    {
        $string = '['.date('Y-m-d H:i:s').'] '.$string;
        return parent::line($string, $style, $verbosity);
    }
}
