<?php
namespace App\Helpers;

use App\Models\Language;

class LangHelper {
    public static function getLangsActives()
    {
      $lang_actives_tmp = Language::activated()->select(array('default_locale'))->get();
      $lang_actives = array();
      foreach($lang_actives_tmp as $l)
      {
        $lang_actives[] = $l->default_locale;
      }
      return $lang_actives;
    }
}
