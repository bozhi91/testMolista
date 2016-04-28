<?php

namespace App\Http\Controllers\Admin\Properties;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ServicesController extends Controller
{
    public function __initialize() {
        $this->middleware('permission:property-service');
        parent::__initialize();
    }

    public function index()
    {
        $query = \App\Models\Property\Service::withTranslations();

        // Filter by title
        if ( $this->request->get('title') )
        {
            $query->whereTranslationLike('title', "%{$this->request->get('title')}%");
        }

        $services = $query->orderBy('title')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

        $this->set_go_back_link();

        return view('admin.properties.services.index', compact('services'));    
    }

    public function create()
    {
        $locales = \App\Models\Translation::getCachedLocales();

        return view('admin.properties.services.create', compact('locales'));    
    }

    public function store()
    {
        // Validate
        if ( !$this->validateRequest($this->request->all()) ) 
        {
            return \Redirect::back()->withInput()->with('error', trans('general.messages.error'));
        }

        // Create element
        $service = new \App\Models\Property\Service;
        $service->enabled = $this->request->get('enabled') ? 1 : 0;
        $service->save();

        if ( empty($service->id) )
        {
            return \Redirect::back()->withInput()->with('error', trans('general.messages.error'));
        }

        // save images
        $this->saveImages($service);

        foreach (\App\Models\Translation::getCachedLocales() as $locale => $locale_name)
        {
            $service->translateOrNew($locale)->title = $this->request->input("i18n.title.{$locale}");
            $service->translateOrNew($locale)->description = $this->request->input("i18n.description.{$locale}");
        }
        $service->save();

        return \Redirect::action('Admin\Properties\ServicesController@edit', $service->id)->with('success', trans('admin/properties/services.created'));
    }

    public function edit($id)
    {
        $service = \App\Models\Property\Service::findOrFail($id);
        $locales = \App\Models\Translation::getCachedLocales();

        return view('admin.properties.services.edit', compact('service','locales'));    
    }

    public function update(Request $request, $id)
    {
        // Validate
        if ( !$this->validateRequest($this->request->all(), $id) ) 
        {
            return \Redirect::back()->withInput()->with('error', trans('general.messages.error'));
        }

        // Get element
        $service = \App\Models\Property\Service::findOrFail($id);

        // Update element
        $service->enabled = $this->request->get('enabled') ? 1 : 0;
        $service->save();

        // save images
        $this->saveImages($service);

        foreach (\App\Models\Translation::getCachedLocales() as $locale => $locale_name)
        {
            $service->translateOrNew($locale)->title = $this->request->input("i18n.title.{$locale}");
            $service->translateOrNew($locale)->description = $this->request->input("i18n.description.{$locale}");
        }
        $service->save();

        return \Redirect::action('Admin\Properties\ServicesController@edit', $service->id)->with('success', trans('admin/properties/services.saved'));

    }

    protected function validateRequest($request, $id=false) 
    {
        // General
        $fields = [
            'enabled' => 'boolean',
            'icon' => 'image|max:' . \Config::get('app.property_image_maxsize', 2048),
            'i18n' => 'required|array',
        ];
        if ( !$id ) {
            //$fields['icon'] .= 'required';
        }

        $validator = \Validator::make($request, $fields);
        if ($validator->fails()) 
        {
            return false;
        }

        // i18n fields
        $fields = [
            'title' => 'required|array',
            'description' => 'required|array',
        ];
        $validator = \Validator::make($this->request->get('i18n'), $fields);
        if ($validator->fails()) 
        {
            return false;
        }

        $i18n = $this->request->get('i18n');

        // Title
        $fields = [
            fallback_lang() => 'required|string',
        ];
        $validator = \Validator::make($i18n['title'], $fields);
        if ($validator->fails()) 
        {
            return false;
        }

        return true;
    }

    protected function saveImages($service) 
    {

        // Icon
        if ( $this->request->file('icon') )
        {
            $img_folder = public_path("services");

            $img_name = $this->request->file('icon')->getClientOriginalName();
            while ( file_exists("{$img_folder}/{$img_name}") )
            {
                $img_name = uniqid() . '_' . $this->request->file('icon')->getClientOriginalName();
            }
            $this->request->file('icon')->move($img_folder, $img_name);

            if ( $service->icon )
            {
                @unlink( public_path("services/{$service->icon}") );
            }

            $service->update([ 'icon'=>$img_name ]);
        }

    }
}
