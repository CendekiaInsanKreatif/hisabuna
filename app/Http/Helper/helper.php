<?php

use App\Models\Menu;


function da($data){
    if (is_object($data) && method_exists($data, 'toArray')) {
        $data = $data->toArray();
    }
    echo "<pre>";
    print_r($data, false);
    echo "</pre>";
    exit;
}

function getMenu(){
    $menu = Menu::with('children')->whereNull('parent_id')->get();
    foreach($menu as $key => $item){
        if($item['id'] == 12 && auth()->user()->roles != 'superadmin'){
            unset($menu[$key]);
        }
    }
    return $menu;
}

function hapusTitik($array) {
    return array_map(function($value) {
        return (int) str_replace('.', '', $value);
    }, $array);
}

function terbilang($x) {
  $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

  if ($x < 12) {
      return " " . $angka[$x];
  } elseif ($x < 20) {
      return terbilang($x - 10) . " Belas ";
  } elseif ($x < 100) {
      return terbilang(floor($x / 10)) . " Puluh " . terbilang($x % 10);
  } elseif ($x < 200) {
      return "Seratus" . terbilang($x - 100);
  } elseif ($x < 1000) {
      return terbilang(floor($x / 100)) . " Ratus " . terbilang($x % 100);
  } elseif ($x < 2000) {
      return "Seribu" . terbilang($x - 1000);
  } elseif ($x < 1000000) {
      return terbilang(floor($x / 1000)) . " Ribu " . terbilang($x % 1000);
  } elseif ($x < 1000000000) {
      return terbilang(floor($x / 1000000)) . " Juta " . terbilang($x % 1000000);
  } elseif ($x < 1000000000000) {
      return terbilang(floor($x / 1000000000)) . " Miliar " . terbilang($x % 1000000000);
  } elseif ($x < 1000000000000000) {
      return terbilang(floor($x / 1000000000000)) . " Triliun " . terbilang($x % 1000000000000);
  }
  return ''; // To handle cases where the number is zero or negative
}

function formatNomorAkun($nomor_akun) {
    $formatted = str_pad($nomor_akun, 8, '0', STR_PAD_RIGHT);
    if (strlen($formatted) >= 3) {
        $formatted = substr($formatted, 0, 3) . '-' . substr($formatted, 3);
    }
    if (strlen($formatted) >= 6) {
        $formatted = substr($formatted, 0, 6) . '-' . substr($formatted, 6);
    }
    return $formatted;
}





  function buildTree($elements, $parentId = 0) {
    $branch = [];
    foreach ($elements as $element) {
        if ($element->parent_id == $parentId) {
            $children = buildTree($elements, $element->id);
            if ($children) {
                $element->children = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

function flattenTree($tree, &$flatArray, $level = 0) {
  foreach ($tree as $node) {
      $node->level = $level;
      $flatArray[] = $node;
      if (isset($node->children)) {
          flattenTree($node->children, $flatArray, $level + 1);
      }
  }
}
