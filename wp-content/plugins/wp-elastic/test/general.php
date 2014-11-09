<?php

die( '<pre>' . print_r( wp_elastic()->get(), true ) . '</pre>' );
die( '<pre>' . print_r( wp_elastic()->get( 'version' ), true ) . '</pre>' );
