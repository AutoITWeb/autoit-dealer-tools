<?php
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class TextUtils {
        public static function GetSlug($txt)
        {
            $slug = TextUtils::Sanitize($txt);
            return trim($slug) === '' ? TextUtils::Sanitize(__('Unknown', 'biltorvet-dealer-tools')) : $slug;
        }

        /**
         * Allows a hash character.
         */
        public static function SanitizeHTMLColor($txt)
        {
            return TextUtils::Sanitize($txt, array('#'));
        }

        /**
         * Sanitize JSON strings
         */
        public static function SanitizeJSON($txt)
        {
            return TextUtils::Sanitize($txt, array('{', '}', ' ', ',', ';', '[', ']', 'å', 'ø', 'æ', 'š', 'ë', '"', '&'), true);
        }

        /**
         * Sanitize any regular text
         */
        public static function SanitizeText($txt)
        {
            return TextUtils::Sanitize($txt, array(' ', 'å', 'ø', 'æ'), true);
        }

        /**
         * This is the most strict sanitization. By default, it removes any special characters and spaces are replaced by dashes. Translates danish special characters.
         */
        public static function Sanitize($txt, $extraAllowedChars = null, $skipTranslation = false)
        {
            $_out = '';
            $_txt = $txt;
            if(!$skipTranslation)
            {
                $_txt = str_replace(' ', '-', $_txt);
                $_txt = str_replace('å', 'aa', $_txt);
                $_txt = str_replace('ø', 'oe', $_txt);
                $_txt = str_replace('æ', 'ae', $_txt);
                $_txt = str_replace('ë', 'e', $_txt); // Citroën
                $_txt = str_replace('š', 's', $_txt);
                $_txt = str_replace('Å', 'AA', $_txt);
                $_txt = str_replace('Ø', 'OE', $_txt);
                $_txt = str_replace('Æ', 'AE', $_txt);
                $_txt = str_replace('Ë', 'E', $_txt);
                $_txt = str_replace('Š', 'S', $_txt); // Škoda
            }
            $allowedChars = array(
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z',
                '0',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
                '-',
                '_'
            );
            if($extraAllowedChars !== null)
            {
                $allowedChars = array_merge($allowedChars, $extraAllowedChars);
            }

            for($i = 0; $i < strlen($_txt); $i++)
            {
                $letter = substr($_txt, $i, 1);
                if(in_array(strtolower($letter), $allowedChars))
                {
                    $_out .= $letter;
                }
            }
            
            return $_out;
        }

        public static function GetVehicleIdentification($vehicle) {
            return  $vehicle->makeName .' ' . $vehicle->model . ' ' . $vehicle->variant . (isset($vehicle->fuel) ? ', ' . $vehicle->fuel : '') . (isset($vehicle->mileage) ? ', ' . __('Mileage', 'biltorvet-dealer-tools') . ': ' . $vehicle->mileage : '') . (isset($vehicle->price) ? ', ' . __('Price', 'biltorvet-dealer-tools') . ': ' . $vehicle->price : '');
        }

        public static function GenerateEquipmentTable($array)
        {
            $cols = null;
            foreach($array as $a)
            {
                $content = $a['key'] . ($a['value'] == null ? '' : ': ' . '<strong>'. $a['value'] .'</strong>');
                $cols .= '<div class="col-6 col-lg-3">' . $content . '</div>';
            }
            if($cols !== null)
            {
                return '<div class="bdt_table row">' . $cols . '</div>';
            }
        }

        public static function GenerateAdditionalEquipmentTable($array)
        {
            $content = '';
            $additionalEquipmentContentItems = '';

            foreach($array as $a)
            {
                if(!isset($a->value) || trim($a->value) === '')
                {
                    continue;
                }

                $additionalEquipmentContentItems .= '<div class="col-6 col-sm-4 col-lg-3 ' . $a->id . '"><img src="' . $a->images[0] . '" loading="lazy" alt="' . $a->id . '" class="additionalequipment_image"><p class="additionalequipmentlabel">' . $a->publicName . '</p><p class="bdt_additionalequipment_value">' . (isset($a->valueFormatted) && $a->valueFormatted != '' ? $a->valueFormatted : $a->value) . '</p></div>';
                $additionalEquipmentContent = '<div class="row">' . $additionalEquipmentContentItems . '</div>';

                $content = '<div class="contentColumn">' . $additionalEquipmentContent . '</div></div>';
            }

            return $content;
        }

        public static function GenerateSpecificationsTable($array)
        {
            $groups = array();
            $content = '';
            $root = dirname(plugin_dir_url( __FILE__ ));

            // Get only groups represented in the results
            foreach($array as $a)
            {
                if(isset($a->group) && !in_array($a->group, $groups))
                {
                    array_push($groups, $a->group);
                }
            }
            
            foreach($groups as $group)
            {
                $icon = '<span class="bticon bticon-Group' . TextUtils::Sanitize($group) . ' bdt_color"></span>';
                $groupPropertiesContentItems = '';
                foreach($array as $a)
                {
                    if(!isset($a->value) || trim($a->value) === '')
                    {
                        continue;
                    }
                    if(isset($a->group) && $a->group === $group)
                    {
                        $groupPropertiesContentItems .= '<div class="col-6 col-sm-4 col-lg-3 ' . $a->id . '"><p class="bdt_spec_label">' . $a->publicName . '</p><p class="bdt_spec_value">' . (isset($a->valueFormatted) && $a->valueFormatted != '' ? $a->valueFormatted : $a->value) . '</p></div>';
                    }
                }
                $groupPropertiesContent = '<h5>' . __($group, 'biltorvet-dealer-tools') . '</h5>';
                $groupPropertiesContent .= '<div class="row">' . $groupPropertiesContentItems . '</div>';

                $content .= '<div class="specificationsGroup">' . ($icon !== null ? '<div class="iconColumn">' . $icon . '</div>' : '' ) . '<div class="contentColumn">' . $groupPropertiesContent . '</div></div>';
            }

            return $content;
        }
    }