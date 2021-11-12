<?php

function fifteenten_ga_scripts($id)
{
    
        var_dump($id);
        wp_die();
    ?>

    <script>
        // Define dataLayer and the gtag function.
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        // Default ad_storage to 'denied'.
        gtag('consent', 'default', {
            'ad_storage': 'denied',
            'analytics_storage': 'denied',
        });
        
    </script>

    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-XXXXXX');</script>

    <script>
        function consentGranted() {
            gtag('consent', 'update', {
                'ad_storage': 'granted'
                'analytics_storage': 'granted',
                });
            }
    </script>


    <?
}