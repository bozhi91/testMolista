<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use Translatable;
    public $translatedAttributes = ['value'];

    protected $guarded = [];

    static public function getCachedLocales() 
    {
		return \Config::get('translatable.locales_select');
    }

    public function getI18nAttribute()
    {
    	$i18n = [];

    	foreach ($this->translations as $t)
    	{
    		$i18n[$t->locale] = $t->value;
    	}

		return $i18n;
    }

    static public function cleanValue($value) 
    {
        // Remove line breaks
        $value = preg_replace( "/\r|\n/", "", $value );
        // Remove leading/trailing spaces
        $value = trim($value);

		return $value;    	
    }

    static public function compileTranslation($file,$locale)
    {
        $translations = \DB::table('translations as t')
                            ->join('translations_translations as ti', function ($join){
                                $join->on('t.id', '=', 'ti.translation_id');
                            })
                            ->selectRaw("t.`tag`, ti.`value`")
                            ->where('t.file', '=', \DB::raw("'{$file}'"))
                            ->where('ti.locale', '=', \DB::raw("'{$locale}'"))
                            ->where('ti.value','!=','')
                            ->whereNotNull('ti.value')
                            ->orderBy('t.tag','asc')
                            ->lists('value','tag');

        //save file
        $file_content = '<?'.'php'."\n";
        $file_content .= "//created on ".date("Y-m-d H:i:s")."\n";
        $file_content .= 'return '.var_export($translations, true).";\n";

        $simple_path = "resources/lang/{$locale}/{$file}.php";

        $path_parts = explode('/', $simple_path);

        $file_name = array_pop($path_parts);

        $file_path = base_path();
        foreach ($path_parts as $fp) {
            $file_path .= '/'.$fp;
            if (is_dir($file_path)) continue;
            mkdir($file_path, 0755, true);
        }

        $file_name = "{$file_path}/{$file_name}";

        if (file_exists($file_name)) {
            unlink($file_name);
        }

        file_put_contents($file_name, $file_content);

        return true;
    }

}
