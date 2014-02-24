<?php

    return [

        /*
         * What should each form element be
         * wrapped within?
        */
        'wrapper' => 'div',

        /*
         * What class should the wrapper
         * element receive?
        */
        'wrapperClass' => 'form-group',

        /**
         * Should form inputs receive a class name?
         */
        'inputClass' => 'form-control',

        /**
         * Frequent input names can map
         * to their respective input types.
         *
         * This way, you may do FormField::description()
         * and it'll know to automatically set it as a textarea.
         * Otherwise, do FormField::thing(['type' => 'textarea'])
         *
         */
        'commonInputsLookup'  => [
            'email' => 'email',
            'emailAddress' => 'email',
            'descrizione_estesa' => 'textarea',
            'bio' => 'textarea',
            'body' => 'textarea',
            'lingua' => "select",
            'password' => 'password',
            'password_confirmation' => 'password'
        ]
    ];
