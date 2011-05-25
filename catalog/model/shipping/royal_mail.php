<?php
class ModelShippingRoyalMail extends Model {
	function getQuote($address) {
		$this->load->language('shipping/royal_mail');

		if ($this->config->get('royal_mail_status')) {
      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('royal_mail_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

      		if (!$this->config->get('royal_mail_geo_zone_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}

		$quote_data = array();

		$home_countries = explode(',', 'GB,CI');

		if ($status) {
			$weight = $this->cart->getWeight();
			$sub_total = $this->cart->getSubTotal();

			$compensation_rates = array(
				'1st_class' 	   => 46,
				'special_delivery' => 2500,
				'standard_parcels' => 500,
				'airsure' 	   => 500,
					);

			if ($this->config->get('royal_mail_1st_class_standard') && in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['1st_class']) {
				$cost = 0;
				$compensation = 0;

				$rates = explode(',', '.1:1.58,.25:1.96,.5:2.48,.75:3.05,1:3.71,1.25:4.90,1.5:5.66,1.75:6.42,2:7.18,4:8.95,6:12.00,8:15.05,10:18.10');


				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				$rates = explode(',', '39:0,100:1,250:2.25,500:3.5');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}

						break;
					}
				}

				if ((float)$cost) {
					$title = $this->language->get('text_1st_class_standard');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_1st_class') . ')';
					}

					$quote_data['1st_class_standard'] = array(
						'id'           => 'royal_mail.1st_class_standard',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_1st_class_recorded') && in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['1st_class']) {
				$cost = 0;
				$compensation = 0;

            			$rates = explode(',', '.1:2.35,.25:2.73,.5:3.25,.75:3.82,1:4.86,1.25:5.67,1.5:6.43,1.75:7.19,2:7.95,4:9.72,6:12.77,8:15.82,10:18.87');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				$rates = explode(',', '39:46,100:46,250:46,500:46');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}

						break;
					}
				}

				if ((float)$cost>$this->config->get('royal_mail_basecost')) {
					$title = $this->language->get('text_1st_class_recorded');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_1st_class') . ')';
					}

					$quote_data['1st_class_recorded'] = array(
						'id'           => 'royal_mail.1st_class_recorded',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_2nd_class_standard') && in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['1st_class']) {
				$cost = 0;

				$rates = explode(',', '.1:1.33,.25:1.72,.5:2.16,.75:2.61,1:3.15');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				if ((float)$cost) {
					$title = $this->language->get('text_2nd_class_standard');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_2nd_class') . ')';
					}

					$quote_data['2nd_class_standard'] = array(
						'id'           => 'royal_mail.2nd_class_standard',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_2nd_class_recorded') && in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['1st_class']) {
				$cost = 0;
				$compensation = 0;

            			$rates = explode(',', '.1:2.10,.25:2.49,.5:2.93,.75:3.38,1:3.92');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				$rates = explode(',', '39:46,100:46,250:46,500:46');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}

						break;
					}
				}

				if ((float)$cost) {
					$title = $this->language->get('text_2nd_class_recorded');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_2nd_class') . ')';
					}

					$quote_data['2nd_class_recorded'] = array(
						'id'           => 'royal_mail.2nd_class_recorded',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_special_delivery') && in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['special_delivery']) {
				$cost = 0;
				$compensation = 0;

				if ($sub_total <= 500) {
					$rates = explode(',', '.1:5.45,.5:5.90,1:7.00,2:9.05,10:22.70');
					$compensation = 500;
				} elseif ($sub_total <= 1000) {
					$rates = explode(',', '.1:6.35,.5:6.80,1:7.90,2:9.95,10:23.60');
					$compensation = 1000;
				} else {
					$rates = explode(',', '.1:8.20,.5:8.65,1:9.75,2:11.80,10:25.45');
					$compensation = $compensation_rates['special_delivery'];
				}

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				if ((float)$cost) {
					$title = $this->language->get('text_special_delivery');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_special_delivery') . ')';
					}

					$quote_data['special_delivery'] = array(
						'id'           => 'royal_mail.special_delivery',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_standard_parcels') && in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['standard_parcels']) {
				$cost = 0;
				$compensation = 0;

				if ($sub_total <= $compensation_rates['1st_class']) {
					$rates = explode(',', '2:4.41,4:7.62,6:10.34,8:12.67,10:13.61,20:15.86');
					$compensation = $compensation_rates['1st_class'];
				} elseif ($sub_total <= 100) {
					$rates = explode(',', '2:6.49,4:10.34,6:13.61,8:16.40,10:17.53,20:20.23');
					$compensation = 100;
				} elseif ($sub_total <= 250) {
					$rates = explode(',', '2:8.29,4:12.14,6:15.41,8:18.20,10:19.33,20:22.03');
					$compensation = 250;
				} else {
					$rates = explode(',', '2:9.49,4:13.34,6:16.61,8:19.40,10:20.53,20:23.33');
					$compensation = $compensation_rates['standard_parcels'];
				}
				

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				if ((float)$cost) {
					$title = $this->language->get('text_standard_parcels');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_standard_parcels') . ')';
					}

					$quote_data['standard_parcels'] = array(
						'id'           => 'royal_mail.standard_parcels',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			$airsure_eu = explode(',', 'AT,BE,DK,EE,FI,FR,DE,LU,NL,PT,IE,ES,SE,AD,FO,IS,LI,MC,CH');
			$airsure_row = explode(',', 'BR,CA,HK,MY,SG,NZ,US');

			if ($this->config->get('royal_mail_airsure') && ( (in_array($address['iso_code_2'], $airsure_eu) && $sub_total <= $compensation_rates['airsure']) || (in_array($address['iso_code_2'], $airsure_row)&& $sub_total <= 250) )) {
				$cost = 0;
				$compensation = 0;
				$insurance = 0;

				$rates = array();

				if (in_array($address['iso_code_2'], $airsure_eu)) {
					$rates = explode(',', '0.1:7.67,0.12:7.81,0.14:8.03,0.16:8.20,0.18:8.41,0.2:8.51,0.22:8.63,0.24:8.75,0.26:8.87,0.28:8.95,0.3:9.01,0.4:9.67,0.5:10.33,0.6:10.99,0.7:11.65,0.8:12.31,0.9:12.97,1:13.63,1.1:14.29,1.2:14.95,1.3:15.61,1.4:16.27,1.5:16.93,1.6:17.59,1.7:18.25,1.8:18.91,1.9:19.57');
				} else {
					$rates = explode(',', '0.02:7.37,0.02:7.37,0.04:7.37,0.06:7.37,0.08:7.37,0.1:7.37,0.12:7.62,0.14:7.9,0.16:8.2,0.18:8.5,0.2:8.8,0.22:9.1,0.24:9.26,0.26:9.36,0.28:9.46,0.30:9.56,0.4:10.67,0.5:11.78,0.6:12.89,0.7:14,0.8:15.11,0.9:16.22,1:17.33,1.1:18.44,1.2:19.55,1.3:20.66,1.4:21.77,1.5:22.88,1.6:23.99,1.7:25.1,1.8:26.21,1.9:27.32,2:28.43');
				}


				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				$rates = array();

				if (in_array($address['iso_code_2'], $airsure_row)) {
					$rates = explode(',', "{$compensation_rates['1st_class']}:0,{$compensation_rates['airsure']}:3");
				} else {
					$rates = explode(',', "{$compensation_rates['1st_class']}:0,250:2.50");
				}

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[0];
							$insurance = $data[1];
						}

						break;
					}
				}

				$cost += $insurance;

				if ((float)$cost) {
					$title = $this->language->get('text_airsure');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_airsure') . ')';
					}

					$quote_data['airsure'] = array(
						'id'           => 'royal_mail.airsure',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_international_signed') && !in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['airsure']) {
				$cost = 0;
				$compensation = 0;
				$insurance = 0;

				$countries = explode(',', 'AL,AD,AM,AT,AZ,BY,BE,BA,BG,HR,CY,CZ,DK,EE,FO,FI,FR,GE,DE,GI,GR,GL,HU,IS,IE,IT,KZ,KG,LV,LI,LT,LU,MK,MT,MD,MC,NL,NO,PL,PT,RO,RU,SM,SK,SI,ES,SE,CH,TJ,TR,TM,UA,UZ,VA');

				if ($weight <= 2) {
					if (in_array($address['iso_code_2'], $countries)) {
						$rates = explode(',', '0.01:6.44,0.02:6.44,0.04:6.44,0.06:6.44,0.08:6.44,0.1:6.44,0.12:6.56,0.14:6.74,0.16:6.88,0.18:7.06,0.2:7.14,0.22:7.24,0.24:7.34,0.26:7.44,0.28:7.51,0.30:7.56,0.4:8.14,0.5:8.69,0.6:9.24,0.7:9.79,0.8:10.34,0.9:10.89,1:11.44,1.1:11.99,1.2:12.54,1.3:13.09,1.4:13.64,1.5:14.19,1.6:14.74,1.7:15.29,1.8:15.84,1.9:16.39,2:16.94');
						$ship_time = $this->language->get('text_ship_time_airmail_europe');
					} else {
						$rates = explode(',', '0.02:7.02,0.02:7.02,0.04:7.02,0.06:7.02,0.08:7.02,0.1:7.02,0.12:7.27,0.14:7.55,0.16:7.85,0.18:8.15,0.2:8.45,0.22:8.75,0.24:8.91,0.26:9.01,0.28:9.11,0.30:9.21,0.4:10.32,0.5:11.43,0.6:12.54,0.7:13.65,0.8:14.76,0.9:15.87,1:16.98,1.1:18.09,1.2:19.2,1.3:20.31,1.4:21.42,1.5:22.53,1.6:23.64,1.7:24.75,1.8:25.86,1.9:26.97,2:28.08');
						$ship_time = $this->language->get('text_ship_time_airmail_world');
					}
				} else {
					$rates = explode(',', '.1:1.21,.15:1.50,.2:1.89,.25:2.28,.3:2.64,.35:3.02,.4:3.42,.45:3.79,.5:4.16,.55:4.50,.6:4.84,.65:5.18,.7:5.52,.75:5.86,.8:6.2,.85:6.54,.9:6.88,.95:7.22,1:7.56,1.1:8.42,1.15:8.58,1.2:8.92,1.25:9.26,1.3:9.6,1.35:9.94,1.4:10.28,1.45:10.62,1.5:10.96,1.6:11.64,1.7:12.32,1.8:13.00,1.9:13.68,2:14.36');
					$ship_time = $this->language->get('text_ship_time_surface');
				}
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', "{$compensation_rates['1st_class']}:0,{$compensation_rates['airsure']}:2.50");
				} else {
					$rates = explode(',', "{$compensation_rates['1st_class']}:0,{$compensation_rates['airsure']}:2.50");
				}

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[0];
							$insurance = $data[1];
						}

						break;
					}
				}

				$cost += $insurance;

				if ((float)$cost) {
					$title = $this->language->get('text_international_signed');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $ship_time . ')';
					}

					$quote_data['international_signed'] = array(
						'id'           => 'royal_mail.international_signed',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('royal_mail_airmail') && !in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['1st_class']) {
				$cost = 0;
				$in_eu = FALSE;

				$countries = explode(',', 'AL,AD,AM,AT,AZ,BY,BE,BA,BG,HR,CY,CZ,DK,EE,FO,FI,FR,GE,DE,GI,GR,GL,HU,IS,IE,IT,KZ,KG,LV,LI,LT,LU,MK,MT,MD,MC,NL,NO,PL,PT,RO,RU,SM,SK,SI,ES,SE,CH,TJ,TR,TM,UA,UZ,VA');

				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', '0.01:1.49,0.02:1.49,0.04:1.49,0.06:1.49,0.08:1.49,0.1:1.49,0.12:1.61,0.14:1.79,0.16:1.93,0.18:2.11,0.2:2.19,0.22:2.29,0.24:2.39,0.26:2.49,0.28:2.56,0.30:2.61,0.4:3.19,0.5:3.74,0.6:4.29,0.7:4.84,0.8:5.39,0.9:5.94,1:6.49,1.1:7.04,1.2:7.59,1.3:8.14,1.4:8.69,1.5:9.24,1.6:9.79,1.7:10.34,1.8:10.89,1.9:11.44,2:11.99');
					$ship_time = $this->language->get('text_ship_time_airmail_europe');
					$in_eu = TRUE;
				} else {
					$rates = explode(',', '0.02:2.07,0.02:2.07,0.04:2.07,0.06:2.07,0.08:2.07,0.1:2.07,0.12:2.32,0.14:2.60,0.16:2.90,0.18:3.20,0.2:3.50,0.22:3.80,0.24:3.96,0.26:4.06,0.28:4.16,0.30:4.26,0.4:5.37,0.5:6.48,0.6:7.59,0.7:8.7,0.8:9.81,0.9:10.92,1:12.03,1.1:13.14,1.2:14.25,1.3:15.36,1.4:16.47,1.5:17.58,1.6:18.69,1.7:19.8,1.8:20.91,1.9:22.02,2:23.13');
					$ship_time = $this->language->get('text_ship_time_airmail_world');
				}

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				if ((float)$cost) {
					$title = $this->language->get('text_airmail');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $ship_time . ')';
					}

					$quote_data['airmail'] = array(
						'id'           => 'royal_mail.airmail',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('royal_mail_surface') && !in_array($address['iso_code_2'], $home_countries) && $sub_total <= $compensation_rates['1st_class']) {
				$cost = 0;
				$compensation = 0;

				$rates = explode(',', '.1:1.21,.15:1.50,.2:1.89,.25:2.28,.3:2.64,.35:3.02,.4:3.42,.45:3.79,.5:4.16,.55:4.50,.6:4.84,.65:5.18,.7:5.52,.75:5.86,.8:6.2,.85:6.54,.9:6.88,.95:7.22,1:7.56,1.1:8.42,1.15:8.58,1.2:8.92,1.25:9.26,1.3:9.6,1.35:9.94,1.4:10.28,1.45:10.62,1.5:10.96,1.6:11.64,1.7:12.32,1.8:13.00,1.9:13.68,2:14.36');

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ($this->config->get('royal_mail_basecost')) {
					$cost += $this->config->get('royal_mail_basecost');
				}

				if ((float)$cost>$this->config->get('royal_mail_basecost')) {
					$title = $this->language->get('text_surface');

					if ($this->config->get('royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if ($this->config->get('royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if ($this->config->get('royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_ship_time') . ' ' . $this->language->get('text_ship_time_surface') . ')';
					}

					$quote_data['surface'] = array(
						'id'           => 'royal_mail.surface',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('royal_mail_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('royal_mail_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}
		}

		$method_data = array();

		if ($quote_data) {
			$method_data = array(
				'id'         => 'royal_mail',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('royal_mail_sort_order'),
				'error'      => FALSE
			);
		}

		return $method_data;
	}
}
?>
