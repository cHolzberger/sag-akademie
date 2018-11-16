<content>

    <?


    ob_start();
    print_r(apc_cache_info());
    $cnt = ob_get_contents();
    ob_end_clean();
    echo nl2br($cnt);
    ?>

    Clear cache...
    <?
    apc_clear_cache();
    apc_clear_cache("user");
    echo "Done<br/>";
    ?>

</content>