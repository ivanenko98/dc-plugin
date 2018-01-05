<?php
session_start();

require_once('IProClient.php');
/*
   Plugin Name: DC Booking
   Author: Tkalenko T., nchvn, Ivanenko O., Grass Business Labs
   Description: DC Booking Plugin allows seamless integration between iPro and Wordpress by using shortcodes.
   Version: 1.0
*/
add_action('admin_menu', 'dcb_admin_menu');
add_action('admin_init', 'load_admin_resources');
add_action('wp_enqueue_scripts', 'load_plugin_resources');


function dcb_admin_menu()
{

    add_menu_page('Booking', 'Booking', 'manage_options', 'dc-booking-top', 'dcb_settings_page');

    add_submenu_page('dc-booking-top', 'Settings', 'Settings', 8, 'dc-booking-top', 'dcb_settings_page');
    add_submenu_page('dc-booking-top', 'Create Shortcode', 'Create Shortcode', 8, 'booking-create-shortcode', 'dcb_create_shortcode_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('dc-booking-top', 'Custom Shortcode', 'Custom Shortcode', 8, 'booking-about', 'dcb_custom_shortcode_page');

}

function load_admin_resources()
{
    wp_enqueue_style('admin-style', plugin_dir_url(__FILE__) . '/css/admin-style.css');
    wp_enqueue_style('ui-datepicker-style', plugin_dir_url(__FILE__) . '/css/jquery-ui.css');

    wp_enqueue_script('jquery', plugin_dir_url(__FILE__) . '/js/jquery-3.2.1.min.js');
    wp_enqueue_script('ui-datepicker.js', plugin_dir_url(__FILE__) . '/js/jquery-ui.min.js');
    wp_enqueue_script('admin.js', plugin_dir_url(__FILE__) . '/js/admin.js');
}

function load_plugin_resources()
{
    wp_enqueue_style('style', plugin_dir_url(__FILE__) . '/css/style.css');
    wp_enqueue_style('ui-datepicker-style', plugin_dir_url(__FILE__) . '/css/jquery-ui.css');
    wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . '/css/font-awesome.min.css');

    wp_enqueue_script('jquery', plugin_dir_url(__FILE__) . '/js/jquery-3.2.1.min.js');
    wp_enqueue_script('main.js', plugin_dir_url(__FILE__) . '/js/main.js');
    wp_enqueue_script('ui-datepicker.js', plugin_dir_url(__FILE__) . '/js/jquery-ui.min.js');
}


function dcb_settings_page()
{
    $host = '139.59.213.223'; // адрес сервера
    $database = 'dc-plugin-server'; // имя базы данных
    $table = 'users';
    $user = 'dorsetcreative'; // имя пользователя
    $password = 'dorset04012018'; // пароль
    ?>

    <div class="wrap booking-plugin">
        <div class="plugin-header">
            <h1>Settings</h1>
            <div class="status">
                <span class="redcircle"></span>
                <span class="redcircle ipro-status"></span>
            </div>
        </div>
        <?php
        if ($_POST['secret_key']) {

            $secret_key = htmlspecialchars($_POST['secret_key']);

            $link = mysqli_connect($host, $user, $password, $database);

            $query = "SELECT * FROM $table WHERE secret_key = '$secret_key'";

            $_SESSION['user'] = mysqli_query($link, $query)->fetch_assoc();

            $_SESSION['secret_key'] = $_POST['secret_key'];

            if ($_SESSION['ipro-secret_key']) {
                unset ($_SESSION['ipro-secret_key']);
            }
            if ($_SESSION['ipro-client_id']) {
                unset ($_SESSION['ipro-client_id']);
            }
        }
        ?>

        <div class="plugin-content">
            <div class="settings-form">
                <form name="form1" method="post" action="">
                    <table class="form-table">
                        <tr valign="top">
                            <td>Dorest Creative Key:</td>
                            <td class="key-field"><input type="text" name="secret_key"
                                                         value="<?php echo $_SESSION['secret_key'] ?>"></td>
                            <td><input type="submit" name="check-user" value="Save" class="button-submit"></td>
                        </tr>
                    </table>
                </form>
            </div>

            <?php

            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            if ($_SESSION['user']) {

                $ipro_client_id = $_POST['ipro-client_id'];
                $ipro_secret_key = $_POST['ipro-secret_key'];
                if ($ipro_client_id && $ipro_secret_key) {
                    $_SESSION['ipro-client_id'] = $_POST['ipro-client_id'];
                    $_SESSION['ipro-secret_key'] = $_POST['ipro-secret_key'];
                    connectToIPro();
                }

                echo '<span class="greencircle"></span>';
                ?>
                <div class="wrap-block">
                    <h2>Settings</h2>
                    <div class="settings-form">
                        <form action="" method="post">
                            <table class="form-table">
                                <tr valign="top">
                                    <td scope="row">ClientID:</td>
                                    <td><input id="ipro-id" type="text" name="ipro-client_id"
                                               value="<?php echo $_SESSION['ipro-client_id'] ?>"></td>
                                </tr>
                                <tr valign="top">
                                    <td scope="row">Secret Key:</td>
                                    <td class="key-field"><input id="ipro-key" type="text" name="ipro-secret_key"
                                                                 value="<?php echo $_SESSION['ipro-secret_key'] ?>">
                                    </td>
                                    <td><input type="submit" name="check-user" value="Save" class="button-submit"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="wrap-block">
                    <h2>Shortcode</h2>
                    <div class="settings-form">
                        <form action="" method="post">
                            <table class="form-table">
                                <tr valign="top">
                                    <td scope="row">Search Page results</td>
                                    <td><input id="ipro-id" type="text" name="ipro-client_id"
                                               value=""></td>
                                </tr>
                                <tr valign="top">
                                    <td scope="row">Search Button JS</td>
                                    <td><textarea type="submit" name="check-user" value="Save" class="button-submit"
                                                  cols="70" rows="6"></textarea></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>


                <?php
                if ($_SESSION['client'] && $_SESSION['user']) {
                    echo '<span class="greencircle ipro-status"></span>';
                } else {
                    echo '<span class="redcircle ipro-status"></span>';

                }
            } else {
                echo '<span class="redcircle"></span>';
                unset($_SESSION['client']);
            } ?>
        </div>
    </div>

    <?php
}

function dcb_create_shortcode_page()
{
    if ($_SESSION['user'] && $_SESSION['client']) {
        require_once('IProClient.php');

        $host = 'localhost'; // адрес сервера
        $database = 'dc-plugin-server'; // имя базы данных
        $table = 'shortcodes';
        $user = 'dorsetcreative'; // имя пользователя
        $password = 'dorset04012018'; // пароль

        ?>
        <div class="wrap booking-plugin">
            <div class="plugin-header">
                <h1>Create Shortcode</h1>
            </div>
            <div class="plugin-content">
                <div class="wrap-block">

                    <?php

                    $client = connectToIPro();

                    $result = $client->executeRequest($client->host . '/apis/properties', [], 'GET', [], 1); ?>
                    <div class="settings-form">
                        <form action="" method="post">
                            <table class="form-table">
                                <tr valign="top">
                                    <td scope="row">Select a Cottage</td>
                                    <td class="key-field">
                                        <select name="create_shortcode">
                                            <?php
                                            foreach ($result['result'] as $item) { ?>
                                                <option data-name="<?= $item['Title'] ?>"
                                                        value="<?= $item['Id'] ?>"><?= $item['Title'] ?></option>
                                                <?php
                                            } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" name="submit" value="Generate shortcode"
                                               class="button-submit">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="generated-shr">
                <div class="caption"><h1>Generated Shortcodes</h1></div>
                <div class="plugin-content">
                    <div class="wrap-block">
                        <table class="form-table generated-shortcodes">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Book Now</th>
                                <th>Availability</th>
                                <th>Reviews</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            if ($_POST['create_shortcode']) {

                                $create_shortcode = $_POST['create_shortcode'];

                                //Add shortcode to database
                                generateShortCode($create_shortcode);

                            }

                            $link = mysqli_connect($host, $user, $password, $database);
                            $user_id = $_SESSION['user']["user_id"];
                            $query = "SELECT * FROM $table WHERE user_id = '$user_id'";

                            $shortcodes = mysqli_query($link, $query);

                            foreach ($shortcodes as $shortcode) { ?>
                                <tr>
                                    <td><span><?= $shortcode['name'] ?></span></td>
                                    <td><span><?= $shortcode['book'] ?></span></td>
                                    <td><span><?= $shortcode['availability'] ?></span></td>
                                    <td><span><?= $shortcode['review'] ?></span></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <?php
    } else {
        echo 'You need authorization!';
    }
}


function dcb_custom_shortcode_page()
{
    $host = 'localhost'; // адрес сервера
    $database = 'dc-plugin-server'; // имя базы данных
    $filter_table = 'filter_shortcode';
    $search_table = 'search_shortcode';
    $user = 'dorsetcreative'; // имя пользователя
    $password = 'dorset04012018'; // пароль

    $user_id = $_SESSION['user']["user_id"];
    $link = mysqli_connect($host, $user, $password, $database);

    $client = connectToIPro();

    $result_locations = $client->executeRequest($client->host . '/apis/locations', [], 'GET', [], 1); ?>



    <?php

    $search_location = $_POST['search_location'];
    $date_in = $_POST['date_in'];
    $date_out = $_POST['date_out'];
    $adults = $_POST['adults'];
    $search_button = $_POST['search_button'];


    $url_info_search = [
        'location_id' => $search_location,
        'date_in' => $date_in,
        'date_out' => $date_out,
        'adults' => $adults
    ];

    $client = connectToIPro();

    if ($search_button) {
        if ($search_location || $date_in || $date_out || $adults) {
            $search_url_request = http_build_query($url_info_search);
            $search_url_request_final = $client->host . 'apis/propertysearch?size=15&' . $search_url_request;
            $query = "INSERT INTO $search_table(link, user_id) VALUES ('$search_url_request_final', '$user_id')";
            mysqli_query($link, $query) or die('error');
            $item_request = "SELECT * FROM $search_table WHERE link = '$search_url_request_final' AND user_id = '$user_id'";
            $item = mysqli_query($link, $item_request)->fetch_assoc();
            $id_item = $item['id'];
            $shortcode = '[search id=' . $id_item . ']';
//            echo($shortcode);
            $shortcode_request = "UPDATE $search_table SET shortcode = '$shortcode' WHERE id = $id_item";
            mysqli_query($link, $shortcode_request) or die('error');
        } else {
            echo('Don`t selected items.');
        }
    }

    $all_search_shortcodes = "SELECT * FROM $search_table  ORDER BY id DESC";
    $items = mysqli_query($link, $all_search_shortcodes);
//    foreach ($items as $item1) {
//        echo($item1['link']);
//    }


    ?>
    <div class="wrap">
        <div class="plugin-header">
            <h1>Custom Shortcode</h1>
        </div>
        <div class="description">
            <h2>Custom Shortcode Generator</h2>
            <div class="description-text">
                Select from the items below to create a custom shortcode.<br>
                The generated shortcode will then display the cottages that match the criteria of the shortcodes
            </div>
        </div>
        <div class="title"><h1>Filter:</h1></div>
        <form action="" method="post">
            <div class="plugin-content">
                <div class="flex-box">
                    <div class="column firts">
                        <select name="location">
                            <option selected hidden value="">Location</option>
                            <?php
                            foreach ($result_locations['result'] as $item) {?>
                                <option data-name="<?= $item['Name'] ?>"
                                        value="<?= $item['Id'] ?>"><?= $item['Name'] ?></option>
                                <?php
                                    foreach ($item['Children'] as $children) { ?>
                                        <option data-name="<?= $children['Name'] ?>"
                                                value="<?= $children['Id'] ?>"><?= $children['Name'] ?></option>
                                        <?php
                                    }
                                ?>
                                <?php
                            } ?>
                        </select>
                        <select name="adults">
                            <option selected hidden value="">Adults</option>
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                ?>
                                <option><?= $i ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <select name="children">
                            <option selected hidden value="">Children</option>
                            <option value="">Any</option>
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                ?>
                                <option><?= $i ?></option>
                                <?php
                            }
                            ?>
                        </select>
                      <div>
                          <input type="text" placeholder="Check in Date" id="filter-chin-date" name="checkIn" value="">
                      </div>
                        <div>
                            <input type="text" placeholder="Check in Date" id="filter-chout-date" name="checkout" value="">
                        </div>
                        <select name="flexiblenights">
                            <option selected hidden value="">Flexible nights</option>
                            <option value="3">+/- 3</option>
                            <option value="7">+/- 7</option>
                        </select>
                    </div>
                    <div class="column second">
                        <select name="dogs">
                            <option selected hidden value="">Dogs welcome?</option>
                            <option>Any</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                        <select name="wifi">
                            <option selected hidden value="">Wifi?</option>
                            <option value="">Any</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                        <select name="parking">
                            <option selected hidden value="">Allocated parking?</option>
                            <option value="">Any</option>
                            <option>Yes</option>
                            <option>On road parking</option>
                        </select>
                    </div>
                    <div class="column third">
                        <select name="infants">
                            <option selected hidden value="">Infants</option>
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                ?>
                                <option><?= $i ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <select name="area">
                            <option selected hidden value="">Area sub category</option>
                            <option value="">Any</option>
                            <option>Langton Matravers</option>
                            <option>Worth Matravers</option>
                            <option>Swanage Cottages</option>
                            <option>Studland Cottages</option>
                            <option>Wareham Cottages</option>
                            <option>Corfe Castle Cottages</option>
                            <option>Waymouth Cottages</option>
                            <option>Lulworth Cottages</option>
                            <option>Purbeck Cottages</option>
                            <option>Cottages in Poole</option>
                            <option>Cottages in Wimborne</option>
                            <option>Blandford Cottages</option>
                        </select>
                        <select name="bedrooms">
                            <option selected hidden value="">Bedrooms</option>
                            <option value="">Any</option>
                            <?php
                            for ($i = 1; $i <= 6; $i++) {
                                ?>
                                <option><?= $i ?>+</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="column fourth">
                        <select name="woodburner">
                            <option selected hidden value="">Woodburner</option>
                            <option value="">Any</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                        <select name="distance">
                            <option selected hidden value="">Distances</option>
                            <option value="">Any</option>
                            <option>Walking Distance to Beach</option>
                            <option>Walking Distance to Bars/Restaurants</option>
                            <option>Walking Distance to Supermarket</option>
                            <option>Walking Distance to Town Centre</option>
                            <option>Walking Distance to Public Transport</option>
                            <option>5 miles to the beach</option>
                            <option>Sea View</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            if($_POST['delete-shotcode']){
                $id_shortcode = $_POST['delete-shotcode'];
                unset ($_POST['delete-shotcode']);
                $query = "DELETE FROM filter_shortcode WHERE `id` = $id_shortcode";
                mysqli_query($link, $query) or die('error');
            }
            ?>
            <div class="title"><h1>Generated Shortcodes</h1></div>
            <div class="plugin-content">
                <input type="text" name="shortcode_name" placeholder="Type in name for shortcode"
                       class="input-name-shortcode">
                <input type="submit" value="Generate shortcode" name="filter_button" class="button-submit">

                <?php

                $location = $_POST['location'];
                $dogs = $_POST['dogs'];
                $infants = $_POST['infants'];
                $woodburner = $_POST['woodburner'];
                $adults = $_POST['adults'];
                $wifi = $_POST['wifi'];
                $area = $_POST['area'];
                $distance = $_POST['distance'];
                $children = $_POST['children'];
                $parking = $_POST['parking'];
                $bedrooms = $_POST['bedrooms'];
                $filterbutton = $_POST['filter_button'];
                $name = $_POST['shortcode_name'];
                $flexiblenights = $_POST['flexiblenights'];
                $checkIn = $_POST['checkIn'];
                $checkout = $_POST['checkout'];


                $url_info = [
                    'checkIn' => $checkIn,
                    'checkout' => $checkout,
                    'LocationID' => $location,
                    'Adults' => $adults,
                    'Children' => $children,
                    'DogsWelcome' => $dogs,
                    'Infants' => $infants,
                    'Woodburner' => $woodburner,
                    'Wifi' => $wifi,
                    'AreaSubCategory' => $area,
                    'Distances' => $distance,
                    'AllocatedParking' => $parking,
                    'Bedrooms' => $bedrooms,
                    'flexiblenights'=> $flexiblenights,

                ];

                $client = connectToIPro();

                if ($filterbutton) {
                    if ($location || $dogs || $infants || $woodburner || $adults || $wifi || $area || $distance || $children || $parking || $bedrooms || $checkIn || $checkout || $flexiblenights) {
                        if($name){
                            $filter_url_request = http_build_query($url_info);
                            $filter_url_request_final = $client->host . 'apis/propertysearch?' . $filter_url_request;
                            $query = "INSERT INTO $filter_table(shortcode_name, link, user_id, shortcode) VALUES ('$name', '$filter_url_request_final', '$user_id', null)";
//                        or die('error');
                            if (!mysqli_query($link, $query)) {
                                echo mysqli_error($link);
                            }
                            $item_request = "SELECT * FROM $filter_table  WHERE shortcode_name = '$name' AND link = '$filter_url_request_final' AND user_id = '$user_id'";
                            $item = mysqli_query($link, $item_request)->fetch_assoc();
                            $id_item = $item['id'];
                            $shortcode = '[filter id=' . $id_item . ']';
                            $shortcode_request = "UPDATE $filter_table SET shortcode = '$shortcode' WHERE id = $id_item";
                            mysqli_query($link, $shortcode_request) or die('error');
                            if (isset($_POST['Name_d'])) {
                                unset ($_POST['Name_d']);
                            }
                        }else{
                            echo '<p>Name doesn`t type in.</p>';
                        }
                    }else{
                        echo '<p>Filter doesn`t match.</p>';
                    }
                }



                $all_filter_shortcodes = "SELECT * FROM $filter_table  ORDER BY id DESC";
                $items = mysqli_query($link, $all_filter_shortcodes);

                foreach ($items as $item1) { ?>
                    <div class="generated-shr">
                        <div class="shortcode-name booking-field"><?= $item1['shortcode_name'] ?></div>
                        <div class="shortcode booking-field"><?= $item1['shortcode'] ?></div>
<!--                        <div class="booking-field btn-delete delete-row" data-row_id="--><?//= $item1['id'] ?><!--">Delete</div>-->
                        <button class="delete-row" value="<?= $item1['id'] ?>" type="submit" name="delete-shotcode">Delete</button>
                    </div>
                <?php
                }
                ?>
            </div>
        </form>
    </div>
    <?php
}

function connectToIPro()
{
    $client = new IProClient($_SESSION['ipro-client_id'], $_SESSION['ipro-secret_key'], 'http://booking.dhcottages.co.uk/');
    $_SESSION['client'] = $client;
    $resp = $client->getAccessToken();

    if ($resp['code'] == 200) {
        $result = $resp['result'];
        $client->setAccessToken($result['access_token']);
        $_SESSION['client'] = $client;

        return $client;
    } else {
        unset($_SESSION['client']);
    }
}

function generateShortCode($id_cottage)
{
    $host = 'localhost'; // адрес сервера
    $database = 'dc-plugin-server'; // имя базы данных
    $table = 'shortcodes';
    $user = 'dorsetcreative'; // имя пользователя
    $password = 'dorset04012018'; // пароль

    $client = connectToIPro();
    $cottageBook = $client->executeRequest($client->host . 'apis/property/' . $id_cottage, [], 'GET', [], 1);

    $name = str_replace('\'', '`', $cottageBook['result']['Title']);
    $shortcode = '[cottage id=' . $id_cottage . ']';
    $shortcode_book = '[book id=' . $id_cottage . ']';
    $shortcode_avail = '[avail id=' . $id_cottage . ']';
    $shortcode_rev = '[rev id=' . $id_cottage . ']';

    $link = mysqli_connect($host, $user, $password, $database);
    $user_id = $_SESSION['user']["user_id"];

    $query_book = "SELECT * FROM $table WHERE user_id = '$user_id' AND shortcode = '$shortcode'";
    $query_avail = "SELECT * FROM $table WHERE user_id = '$user_id' AND shortcode = '$shortcode'";
    $query_rev = "SELECT * FROM $table WHERE user_id = '$user_id' AND shortcode = '$shortcode'";
    $query = "SELECT * FROM $table WHERE user_id = '$user_id' AND shortcode = '$shortcode'";

    $book_db = mysqli_query($link, $query_book)->fetch_assoc();
    $avail_db = mysqli_query($link, $query_avail)->fetch_assoc();
    $rev_db = mysqli_query($link, $query_rev)->fetch_assoc();
    $shortcode_db = mysqli_query($link, $query)->fetch_assoc();

    if (!isset($shortcode_db) && !isset($book_db) && !isset($avail_db) && !isset($rev_db)) {
        $query = "INSERT INTO $table(name, shortcode, book, availability, review, user_id) VALUES ('$name', '$shortcode', '$shortcode_book', '$shortcode_avail', '$shortcode_rev', '$user_id')";
        mysqli_query($link, $query) or die('error');
    }
}

function shortcode_callback($atts)
{
    $atts = shortcode_atts(array(
        'id' => 'no id'
    ), $atts, '');

    $client = connectToIPro();
    $cottageDetail = $client->executeRequest($client->host . 'apis/property/' . esc_html($atts['id']), [], 'GET', [], 1);
    $cottageRait = $client->executeRequest($client->host . '/apis/property/' . esc_html($atts['id']) . '/rates?nearestSeason=today', [], 'GET', [], 1);
    $cottageImage = $client->executeRequest($client->host . '/apis/property/' . esc_html($atts['id']) . '/images', [], 'GET', [], 1);

    if (strlen($cottageDetail['result']['MainDescription']) > 200) {
        $main_description = substr($cottageDetail['result']['MainDescription'], 0, 200) . '...';
    } else {
        $main_description = $cottageDetail['result']['MainDescription'];
    }

    $html = $cottageDetail['result']['Name'] . '<pre>' .
        $main_description . '<pre>' .
        $cottageDetail['result']['Currency'] . $cottageDetail['result']['MinRate'] .
        ' - ' .
        $cottageDetail['result']['Currency'] . $cottageDetail['result']['MaxRate'] . '<pre>' .
        $cottageDetail['result']['Country'] . '<pre>' .
        'Bedrooms - ' . $cottageDetail['result']['Attributes']['Bedrooms'][0] . '<pre>' .
        'Bathrooms - ' . $cottageDetail['result']['Attributes']['Bathrooms'][0] . '<pre>' .
        'Sleeps - ' . $cottageDetail['result']['Attributes']['Sleeps'][0] . '<pre>' .
        'Ref: ' . $cottageDetail['result']['PropertyReference'] . '<pre>' .
        '<img src = ' . $cottageImage['result'][0]['Url'] . '>' . '<pre>';

    return $html;
}

function book_callback($atts)
{
    $atts = shortcode_atts(array(
        'id' => 'no id'
    ), $atts, '');

    $client = connectToIPro();
    $cottageBook = $client->executeRequest($client->host . 'apis/property/' . esc_html($atts['id']), [], 'GET', [], 1);
    $cottageAvail = $client->executeRequest($client->host . 'apis/property/' . esc_html($atts['id'] . '/availability'), [], 'GET', [], 1);


    foreach ($cottageAvail['result'] as $onecottage) {
        $disabledates[] = [
            'from' => $onecottage['StartDate'],
            'to' => $onecottage['EndDate']
        ];
    }
    ?>
    <script type='text/javascript'>
        var disabledArr = <?php echo json_encode($disabledates); ?>;
    </script>

    <?php

    $html = '
            <div  class="action-btn">
                <button id="book">Book now</button>
            </div>
            <div class="book-form" data-id="' . $atts['id'] . '">
                <form>
                    <div>
                        <p>
                            <label for="checkin">Check in</label>
                            <input data-id="' . $atts['id'] . '" id="field-checkin" type="text" />
                        </p>
                        <p>
                            <label for="checkout">Check out</label>
                            <input data-id="' . $atts['id'] . '" id="field-checkout" type="text" />
                        </p>
                    </div>
                    <div class="fields-number">
                        <p>
                            <label for="">Adults</label>
                            <input type="number">
                        </p>
                        <p>
                            <label for="">Children</label>
                            <input type="number">
                        </p>
                        <p class="last">
                            <label for="">Infants</label>
                            <input type="number">
                        </p>
                    </div>
                    <div>
                        <input type="submit" value="Book">
                    </div>
                </form>
            </div>';

    return $html;

}

function avail_callback($atts)
{

    $atts = shortcode_atts(array(
        'id' => 'no id'
    ), $atts, '');

    $client = connectToIPro();
    $cottageAvail = $client->executeRequest($client->host . 'apis/property/' . esc_html($atts['id'] . '/availability'), [], 'GET', [], 1);

    $html = '
            <div  class="action-btn">
                <button id="availab">Availability</button>
            </div>
            <div class="availab-calendar-box">
                <div id="availab-calendar"></div>
            </div>';
    return $html;
}

function rev_callback($atts)
{

    $atts = shortcode_atts(array(
        'id' => 'no id'
    ), $atts, '');

    $client = connectToIPro();
    $cottageDetail = $client->executeRequest($client->host . '/apis/reviews?propertyId=' . esc_html($atts['id']), [], 'GET', [], 1);



    $html = '
            <div class="action-btn">
                <button id="reviews">Reviews</button>
            </div>
            <div class="reviews-box">
                <div>
                '.
                    showRating($cottageDetail['result'])
                .'
                
                </div>
            </div>';

    return $html;
}

function filter_callback($atts)
{


    $host = 'localhost'; // адрес сервера
    $database = 'dc-plugin-server'; // имя базы данных
    $filter_table = 'filter_shortcode';
    $user = 'dorsetcreative'; // имя пользователя
    $password = 'dorset04012018'; // пароль
    $link = mysqli_connect($host, $user, $password, $database);


    $atts = shortcode_atts(array(
        'id' => 'no id'
    ), $atts, '');

    $id_link = esc_html($atts['id']);

    $link_request = "SELECT * FROM $filter_table  WHERE id = $id_link";
    $link = mysqli_query($link, $link_request)->fetch_assoc() or die('nahyi');
    $client = connectToIPro();
    $filter_items = $client->executeRequest($link['link'], [], 'GET', [], 1);


    return var_dump($filter_items);
}

function search_callback($atts)
{
    $host = 'localhost'; // адрес сервера
    $database = 'dc-plugin-server'; // имя базы данных
    $search_table = 'search_shortcode';
    $user = 'dorsetcreative'; // имя пользователя
    $password = 'dorset04012018'; // пароль
    $link = mysqli_connect($host, $user, $password, $database);

    $client = connectToIPro();


    $atts = shortcode_atts(array(
        'id' => 'no id'
    ), $atts, '');

    $id_link = esc_html($atts['id']);

    $link_request = "SELECT * FROM $search_table  WHERE id = $id_link";
    $link = mysqli_query($link, $link_request)->fetch_assoc() or die('nahyi');

    $content = $client->executeRequest($link['link'], [], 'GET', [], 1);


}

function showRating($result){
    $html = '';
    foreach ($result as $rating) {

        if ($rating['IsApproved']){
            // rating stars
            $ratingNum = $rating['Rating'];

            $html .= '<p>';
            if (is_float($ratingNum)) {
                for ($i = 1; $i <= intval($ratingNum); $i++) {

                    $html .= '<i class="fa fa-star"></i>';
                }
                $html .= '<i class="fa fa-star-half-o"></i>';
            } else {
                for ($i = 1; $i <= $ratingNum; $i++) {

                    $html .= '<i class="fa fa-star"></i>';
                }
            }
            $html .= '</p>';

            //rating content
            $html .= '<p><strong>' . $rating['ReviewTitle'] . '</strong></p>';
            $html .= '<p>' . $rating['ReviewDescription'] . '</p>';
            $html .= '<p><i>' . $rating['ReviewerName'] . '</i></p>';
        }

    }

    return $html;

}

add_shortcode('filter', 'filter_callback');
add_shortcode('search', 'search_callback');
add_shortcode('cottage', 'shortcode_callback');
add_shortcode('book', 'book_callback');
add_shortcode('avail', 'avail_callback');
add_shortcode('rev', 'rev_callback');


