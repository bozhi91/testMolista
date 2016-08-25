<?php namespace App\Http\Controllers\Account\Profile;

use Illuminate\Http\Request;

use App\Http\Requests;

use Intervention\Image\ImageManagerStatic as Image;

class SignaturesController extends \App\Http\Controllers\AccountController
{
	protected $signatures;

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'profile');
		\View::share('submenu_subsection', 'profile-signatures');

		$this->signatures = $this->site_user->sites_signatures()->ofSite($this->site->id)->orderBy('title')->get();
		\View::share('signatures', $this->signatures);
	}

	public function getIndex()
	{
		return view('account.profile.signatures.index');
	}

	public function getCreate()
	{
		return view('account.profile.signatures.create');
	}
	public function postCreate()
	{
		$data = array_merge($this->request->all(), [
			'site_id' => $this->site->id,
			'user_id' => $this->site_user->id,
		]);
		
		$validator = \App\Models\Site\UserSignature::getCreateValidator($data,false);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$this->prepareSignatureImages($data);

		$item = \App\Models\Site\UserSignature::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->action('Account\Profile\SignaturesController@getEdit', $item->id)->with('success', trans('general.messages.success.saved'));
	}

	public function getEdit($id)
	{
		$signature = $this->site_user->sites_signatures()->ofSite($this->site->id)->findOrFail($id);
		return view('account.profile.signatures.edit', compact('signature'));
	}
	public function postEdit($id)
	{
		$signature = $this->site_user->sites_signatures()->ofSite($this->site->id)->findOrFail($id);

		$data = array_merge($this->request->all(), [
			'site_id' => $this->site->id,
			'user_id' => $this->site_user->id,
		]);
		
		$validator = \App\Models\Site\UserSignature::getUpdateValidator($data,$id);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$this->prepareSignatureImages($data, $signature);

		$item = \App\Models\Site\UserSignature::saveModel($data,$id);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}

	public function deleteRemove($id)
	{
		$signature = $this->site_user->sites_signatures()->ofSite($this->site->id)->findOrFail($id);

		// Delete images
		foreach ($signature->images as $image)
		{
			@unlink($image);
		}

		// Delete signature
		$signature->delete();

		return redirect()->action('Account\Profile\SignaturesController@getIndex')->with('success', trans('account/profile.signatures.deleted'));
	}

	protected function prepareSignatureImages(&$data,$item=false)
	{
		$data['images'] = [];

		if ( empty($data['signature']) )
		{
			return false;
		}

		$signature = $data['signature'];

		// Set image folder
		$image_dir = $this->site_user->image_directory;
		if ( !is_dir( public_path($image_dir) ) )
		{
			\File::makeDirectory(public_path($image_dir), 0777, true, true);
		}

		// Load dom
		$dom = new \DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHtml(mb_convert_encoding($signature, 'HTML-ENTITIES', "UTF-8"), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		// Get images
		$images = $dom->getElementsByTagName('img');
		
		foreach ($images as $img)
		{
			$src = $img->getAttribute('src');

			if ( preg_match('#^'. asset($image_dir) .'/(.*)$#', $src, $matches) )
			{
				$data['images'][] = public_path(str_replace(asset($image_dir), $image_dir, $src));
				continue;
			}

			// If img source is 'data-url'
			if( preg_match('/data:image/', $src) )
			{
				// Get mimetype
				preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
				$mimetype = $groups['mime'];

				// Get file path
				if ( $img->getAttribute('data-filename') )
				{
					$img_name = pathinfo($img->getAttribute('data-filename'), PATHINFO_FILENAME);
					$filepath = "{$image_dir}/{$img_name}.{$mimetype}";
					while ( file_exists( public_path( $filepath ) ) )
					{
						$filepath = "{$image_dir}/" . uniqid() . "_{$img_name}.{$mimetype}";
					}
					$img->removeAttribute('data-filename');
				}
				else
				{
					$filepath = "{$image_dir}/" . uniqid() . ".{$mimetype}";
				}

				// See http://image.intervention.io/api/
				$image = Image::make($src)
					->resize(800, null, function ($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					})
					->encode($mimetype, 100) 	// encode file to the specified mimetype
					->save( public_path( $filepath )	);
			
				$img->removeAttribute('src');
				$img->setAttribute('src', asset($filepath));

				$data['images'][] = public_path($filepath);
			} 

		}

		$data['signature'] = $dom->saveHTML();

		return true;
	}

}
