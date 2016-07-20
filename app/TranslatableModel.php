<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatableModel extends Model
{
	use \Dimsav\Translatable\Translatable;

	public $translatedAttributes = [];

	public $_i18n = [];

	public function scopeWithId($query, $id)
	{
		return $query->where("{$this->getTable()}.id", $id);

	}
	public function scopeWithTranslations($query)
	{
		$table = $this->getTable();
		$translations_table = $this->getTranslationsTable();
		$relation_key = $this->getRelationKey();
		$key_name = $this->getKeyName();
		$locale_key = $this->getLocaleKey();
		$locale = $this->locale();
		$locale_default = \Config::get('translatable.fallback_locale');

		$query->addSelect("{$table}.*");
		if ( !empty($this->translatedAttributes) )
		{
			foreach ($this->translatedAttributes as $attr) 
			{
				$query->addSelect( \DB::raw("IF (i18n.`{$attr}` IS NULL OR i18n.`{$attr}` = '', i18n_default.`{$attr}`, i18n.`{$attr}`) as {$attr}") );
			}
		}

		return $query
			->leftjoin("{$translations_table} AS i18n", function($join) use ($translations_table, $table, $relation_key, $key_name, $locale_key, $locale) {
				$join->on("i18n.{$relation_key}", '=', $table.'.'.$key_name);
				$join->on("i18n.{$locale_key}", '=', \DB::raw("'".$locale."'"));
			})
			->leftjoin("{$translations_table} AS i18n_default", function($join) use ($translations_table, $table, $relation_key, $key_name, $locale_key, $locale_default) {
				$join->on("i18n_default.{$relation_key}", '=', $table.'.'.$key_name);
				$join->on("i18n_default.{$locale_key}", '=', \DB::raw("'".$locale_default."'"));
			})
			;
	}

	public function getI18nAttribute()
	{
		if ( !$this->id || empty($this->translatedAttributes) ) 
		{
			return $this->_i18n;
		}

		if ( !empty($this->_i18n) )
		{
			return $this->_i18n;
		}

		$model_name = '\\' . $this->getTranslationModelName();
		$translations = $model_name::where($this->getRelationKey(), $this->id)->get();

		foreach ($translations as $translation) 
		{
			foreach ($this->translatedAttributes as $key)
			{
				$this->_i18n[$key][$translation->locale] = $translation->$key;
			}
		}

		return $this->_i18n;
	}

	public function getAttribute($key)
	{
		if (str_contains($key, ':')) {
			list($key, $locale) = explode(':', $key);
		} else {
			$locale = $this->locale();
		}

		if ($this->isTranslationAttribute($key)) {
			if ( !empty($this->attributes[$key]) )
			{
				return $this->attributes[$key];
			}

			if ($this->getTranslation($locale) === null) {
				return;
			}

			return $this->getTranslation($locale)->$key;
		}

		return parent::getAttribute($key);
	}

}
