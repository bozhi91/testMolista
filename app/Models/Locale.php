<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{

	static public function getCorporateLocales()
	{
		return [ 'es' ];
	}

	static public function getConfigFilepath()
	{
		$dirpath = storage_path();

		foreach (explode('/', "app/config") as $fp) 
		{
			$dirpath .= '/'.$fp;
			if (is_dir($dirpath)) continue;
			mkdir($dirpath, 0755, true);
		}

		$filepath = $dirpath."/locale.php";

		return $filepath;
	}
	static public function getConfig()
	{
		$filepath = self::getConfigFilepath();
		if ( !file_exists($filepath) )
		{
			return [];
		}

		$config = include $filepath;

		return $config;
	}
	static public function saveConfig()
	{
		$webLocales = self::select('locale','flag','dir','name','script','native','regional')->where('web', 1)->orderBy('native')->get()->keyBy('locale')->toArray();
		$adminLocalesSelect = self::where('admin', 1)->orderBy('native')->lists('native','locale')->toArray();

		$file_content = '<?' . "php\n";
		$file_content .= "// locale configuration - created on " . date("Y-m-d H:i:s") . "\n";
		$file_content .= "return [\n";
		$file_content .= "\t'webLocales' => " . var_export($webLocales, true) . ",\n";
		$file_content .= "\t'adminLocales' => " . var_export(array_keys($adminLocalesSelect), true) . ",\n";
		$file_content .= "\t'adminLocalesSelect' => " . var_export($adminLocalesSelect, true) . ",\n";
		$file_content .= "];\n";

		$filepath = self::getConfigFilepath();
		if (file_exists($filepath)) {
			unlink($filepath);
		}

		return file_put_contents($filepath, $file_content);
	}

	static public function getAdminOptions()
	{
		return self::where('admin',1)->orderBy('native')->lists('native','locale')->toArray();   	
	}

	static public function getScriptOptions()
	{
		return [
			'Arab' => 'Arabic',
			'Armn' => 'Armenian',
			'Bali' => 'Balinese',
			'Batk' => 'Batak',
			'Beng' => 'Bengali',
			'Blis' => 'Blissymbols',
			'Bopo' => 'Bopomofo',
			'Brah' => 'Brahmi',
			'Brai' => 'Braille',
			'Bugi' => 'Buginese',
			'Buhd' => 'Buhid',
			'Cans' => 'Unified Canadian Aboriginal Syllabics',
			'Cari' => 'Carian',
			'Cham' => 'Cham',
			'Cher' => 'Cherokee',
			'Cirt' => 'Cirth',
			'Copt' => 'Coptic',
			'Cprt' => 'Cypriot',
			'Cyrl' => 'Cyrillic',
			'Cyrs' => 'Cyrillic (Old Church Slavonic variant)',
			'Deva' => 'Devanagari (Nagari)',
			'Dsrt' => 'Deseret (Mormon)',
			'Egyd' => 'Egyptian demotic',
			'Egyh' => 'Egyptian hieratic',
			'Egyp' => 'Egyptian hieroglyphs',
			'Ethi' => 'Ethiopic (Geʻez) - Ethiopic (Geʻez)',
			'Geok' => 'Khutsuri (Asomtavruli and Nuskhuri)',
			'Geor' => 'Georgian (Mkhedruli)',
			'Glag' => 'Glagolitic',
			'Goth' => 'Gothic',
			'Grek' => 'Greek',
			'Gujr' => 'Gujarati',
			'Guru' => 'Gurmukhi',
			'Hang' => 'Hangul (Hangŭl, Hangeul)',
			'Hani' => 'Han (Hanzi, Kanji, Hanja)',
			'Hano' => 'Hanunoo (Hanunóo)',
			'Hans' => 'Han (Simplified variant)',
			'Hant' => 'Han (Traditional variant)',
			'Hebr' => 'Hebrew',
			'Hira' => 'Hiragana',
			'Hmng' => 'Pahawh Hmong',
			'Hrkt' => '(alias for Hiragana + Katakana)',
			'Hung' => 'Old Hungarian',
			'Inds' => 'Indus (Harappan)',
			'Ital' => 'Old Italic (Etruscan, Oscan, etc.)',
			'Java' => 'Javanese',
			'Jpan' => 'Japanese (alias for Han + Hiragana + Katakana)',
			'Kali' => 'Kayah Li',
			'Kana' => 'Katakana',
			'Khar' => 'Kharoshthi',
			'Khmr' => 'Khmer',
			'Knda' => 'Kannada',
			'Lana' => 'Lanna',
			'Laoo' => 'Lao',
			'Latf' => 'Latin (Fraktur variant)',
			'Latg' => 'Latin (Gaelic variant)',
			'Latn' => 'Latin',
			'Lepc' => 'Lepcha (Róng)',
			'Limb' => 'Limbu',
			'Lina' => 'Linear A',
			'Linb' => 'Linear B',
			'Lyci' => 'Lycian',
			'Lydi' => 'Lydian',
			'Mand' => 'Mandaean',
			'Maya' => 'Mayan hieroglyphs',
			'Mero' => 'Meroitic',
			'Mlym' => 'Malayalam',
			'Mong' => 'Mongolian',
			'Moon' => 'Moon - Moon code - Moon script - Moon type',
			'Mtei' => 'Meitei Mayek - Meithei - Meetei',
			'Mymr' => 'Myanmar (Burmese)',
			'Nkoo' => 'N’Ko',
			'Ogam' => 'Ogham',
			'Olck' => 'Ol Chiki (Ol Cemet\', Ol, Santali)',
			'Orkh' => 'Orkhon',
			'Orya' => 'Oriya',
			'Osma' => 'Osmanya',
			'Perm' => 'Old Permic',
			'Phag' => 'Phags-pa',
			'Phnx' => 'Phoenician',
			'Plrd' => 'Pollard Phonetic',
			'Rjng' => 'Rejang - Redjang - Kaganga',
			'Roro' => 'Rongorongo',
			'Runr' => 'Runic',
			'Sara' => 'Sarati',
			'Saur' => 'Saurashtra',
			'Sgnw' => 'SignWriting',
			'Shaw' => 'Shavian (Shaw)',
			'Sinh' => 'Sinhala',
			'Sund' => 'Sundanese',
			'Sylo' => 'Syloti Nagri',
			'Syrc' => 'Syriac',
			'Syre' => 'Syriac (Estrangelo variant)',
			'Syrj' => 'Syriac (Western variant)',
			'Syrn' => 'Syriac (Eastern variant)',
			'Tagb' => 'Tagbanwa',
			'Tale' => 'Tai Le',
			'Talu' => 'New Tai Lue',
			'Taml' => 'Tamil',
			'Telu' => 'Telugu',
			'Teng' => 'Tengwar',
			'Tfng' => 'Tifinagh (Berber)',
			'Tglg' => 'Tagalog',
			'Thaa' => 'Thaana',
			'Thai' => 'Thai',
			'Tibt' => 'Tibetan',
			'Ugar' => 'Ugaritic',
			'Vaii' => 'Vai',
			'Visp' => 'Visible Speech',
			'Xpeo' => 'Old Persian',
			'Xsux' => 'Cuneiform, Sumero-Akkadian',
			'Yiii' => 'Yi',
			'Zxxx' => 'Code for unwritten languages',
			'Zyyy' => 'Code for undetermined script',
			'Zzzz' => 'Code for uncoded script',
		];
	}

}
