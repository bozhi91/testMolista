<?php

return [

    'payment_failed.subject' => 'Molista: ERROR al recibir el pago',
    'payment_failed.body' => '<p>Se ha producido un error en un pago de stripe para el siguiente site de Molista:</p>
                            <ul>
                                <li>ID: :site_id</li>
                                <li>Subdomain: :subdomain</li>
                                <li>Válido hasta: :created</li>
                            </ul>',


];
