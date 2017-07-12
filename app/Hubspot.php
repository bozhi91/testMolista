<?php namespace App;

class Hubspot {

    public static function createOrUpdateLead($email, $data = [])
    {
        try
		{
			\HubSpot::contacts()->getByEmail($email);
		}
		catch (\Exception $e)
		{
			$properties = [
				[
					'property' => 'hs_lead_status',
					'value' => 'NEW'
				],
				[
					'property' => 'lifecyclestage',
					'value' => 'opportunity'
				],
				[
					'property' => 'product',
					'value' => 'Molista'
				]
            ];

            foreach ($data as $key => $value)
            {
                $properties []= [
                    'property' => $key,
                    'value' => $value
                ];
            }

			// Not found, then create the lead
			try
            {
                \HubSpot::contacts()->createOrUpdate($email, $properties);
            }
            catch (\Exception $e)
            {
                return false;
            }

            return true;
		}

        return false;
    }

    public static function setAsCustomer($email, $data = [])
    {
        $update = true;

        try
		{
            // If customer doesn't exists, a exception is thrown
			\HubSpot::contacts()->getByEmail($email);
		}
		catch (\Exception $e)
		{
            $update = false;
		}

        if ($update)
        {
            $properties = [
                [
                    'property' => 'hs_lead_status',
                    'value' => 'OPEN'
                ],
                [
                    'property' => 'lifecyclestage',
                    'value' => 'customer'
                ],
                [
                    'property' => 'product',
                    'value' => 'Molista'
                ]
            ];

            foreach ($data as $key => $value)
            {
                $properties []= [
                    'property' => $key,
                    'value' => $value
                ];
            }

            // Not found, then create the lead
            try
            {
                \HubSpot::contacts()->createOrUpdate($email, $properties);
            }
            catch (\Exception $e)
            {
                return false;
            }

            return true;
        }

        return false;
    }

}
