<div id="places-search">
    <p>ID: <?php esc_attr( places_place_id() ); ?></p>
    <p>
        <input type="text" id="places-search-term" name="places-search-term" placeholder="Escribe el nombre"
           value="<?php esc_attr( places_place_name() ); ?>" size="25" style="width:100%;" />
        <input type="hidden" id="places-search-id" name="places-search-id"
           value="<?php esc_attr( places_place_id() ); ?>" />
    </p>
    <p id="place-search-loading" class="hidden">
        <span class="spinner"></span>
        Cargando datos del establecimiento...
    </p>
    
    <?php if ( places_has_place_saved() ) { $place = places_get_detail_place_saved() ?>
        <?php echo $place[''];?>
        <div class="places-info-box">
            <div class="places-img-box">
                <img src="<?php echo places_get_url_photo($place['photos'][0]);?>" />
            </div>
            <div class="places-info-desc">
                <span class="title"><?php echo $place['name'];?></span>
                <ul>
                    <li><span>Dirección: </span><?php echo $place['address'];?></li>
                    <li><span>Telefono: </span><?php echo $place['phoneNumber'];?></li>
                    <li><span>Web: </span><a href="<?php echo $place['website'];?>" target="_blank" ><?php echo $place['website'];?></a></li>
                    <li><span></span><?php echo $place[''];?></li>
                    <li><span></span><?php echo $place[''];?></li>
                    <li><span></span><?php echo $place[''];?></li>
                </ul>
            </div>
        </div>

    <?php /*var_dump($place);*/ } ?>

</div>