<?php

use App\Models\Menu;
use App\Models\Jurnal;


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

function pisah($x) {
    // Taufiq
    preg_match_all('/\d+|[a-zA-Z\s]+/', $x, $a);

    $angka = '';
    $huruf = '';

    foreach ($a[0] as $match) {
        if (is_numeric($match)) {
            $angka .= $match;
        } else {
            $huruf .= $match;
        }
    }

    return $huruf;
}

function formatNomorAkun($nomor_akun) {
    // da($nomor_akun);
    $formatted = str_pad($nomor_akun, 8, '0', STR_PAD_RIGHT);
    if (strlen($formatted) >= 3) {
        $formatted = substr($formatted, 0, 3) . '-' . substr($formatted, 3);
    }
    if (strlen($formatted) >= 6) {
        $formatted = substr($formatted, 0, 6) . '-' . substr($formatted, 6);
    }
    return $formatted;
}

function recursive_ksort(&$array) {
    if (is_array($array)) {
        ksort($array);
        foreach ($array as &$value) {
            // da($value);
            recursive_ksort($value);
        }
    }
    return $array;
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

function getJurnalDetail($jurnal_id)
{
    $jurnal = Jurnal::with('details.coa')->find($jurnal_id);
    return $jurnal;
}


/**
 * Anggap $data berbentuk:
 * $data[<tahun>][<group>][<subgroup>][<akun>] = <nilai number>
 *
 * Aturan:
 * - Jika nilai tahun sekarang DAN tahun sebelumnya sama-sama 0/blank → hapus di KEDUA tahun.
 * - Jika salah satu ≠ 0 → biarkan keduanya tampil.
 * - Setelah itu, hapus array kosong yang tersisa (parent yang tak punya anak).
 */
function pruneZerosPairYears(array $data, int $currentYear, float $eps = 0.0): array
{
    $prevYear = $currentYear - 1;

    // helper: cek nol
    $isZero = function($v) use ($eps): bool {
        if ($v === null) return true;
        if (!is_numeric($v)) return false;
        return abs((float)$v) <= $eps;
    };

    // ambil union key di setiap level agar perbandingan lengkap
    $yearNow  = $data[$currentYear]   ?? [];
    $yearPrev = $data[$prevYear]      ?? [];

    $groups = array_values(array_unique(array_merge(array_keys($yearNow), array_keys($yearPrev))));
    foreach ($groups as $g) {
        $subNow  = $yearNow[$g]  ?? [];
        $subPrev = $yearPrev[$g] ?? [];

        $children = array_values(array_unique(array_merge(array_keys($subNow), array_keys($subPrev))));
        foreach ($children as $c) {
            $leafNow  = $subNow[$c]  ?? [];
            $leafPrev = $subPrev[$c] ?? [];

            // kalau level ini bukan array (aneh), lewati
            if (!is_array($leafNow) && !is_array($leafPrev)) {
                continue;
            }

            $akunKeys = array_values(array_unique(array_merge(
                is_array($leafNow)  ? array_keys($leafNow)  : [],
                is_array($leafPrev) ? array_keys($leafPrev) : []
            )));

            foreach ($akunKeys as $a) {
                $vNow  = $leafNow[$a]  ?? null;
                $vPrev = $leafPrev[$a] ?? null;

                // hanya proses leaf numeric / null
                if (($isZero($vNow) && $isZero($vPrev))) {
                    // hapus di kedua tahun
                    if (isset($data[$currentYear][$g][$c][$a])) unset($data[$currentYear][$g][$c][$a]);
                    if (isset($data[$prevYear][$g][$c][$a]))   unset($data[$prevYear][$g][$c][$a]);
                }
            }

            // bersihkan child kosong
            if (isset($data[$currentYear][$g][$c]) && empty($data[$currentYear][$g][$c])) {
                unset($data[$currentYear][$g][$c]);
            }
            if (isset($data[$prevYear][$g][$c]) && empty($data[$prevYear][$g][$c])) {
                unset($data[$prevYear][$g][$c]);
            }
        }

        // bersihkan group kosong
        if (isset($data[$currentYear][$g]) && empty($data[$currentYear][$g])) {
            unset($data[$currentYear][$g]);
        }
        if (isset($data[$prevYear][$g]) && empty($data[$prevYear][$g])) {
            unset($data[$prevYear][$g]);
        }
    }

    // kalau seluruh tahun kosong, hapus key tahun
    if (isset($data[$currentYear]) && empty($data[$currentYear])) unset($data[$currentYear]);
    if (isset($data[$prevYear])   && empty($data[$prevYear]))   unset($data[$prevYear]);

    return $data;
}
