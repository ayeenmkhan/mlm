<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$strmbr_intro = base64_decode($mbrstr['mbr_intro']);
$mbr_sosmed = get_optionvals($mbrstr['mbr_sosmed']);
$mbrstr['mbr_twitter'] = $mbr_sosmed['mbr_twitter'];
$mbrstr['mbr_facebook'] = $mbr_sosmed['mbr_facebook'];

$country_array = array_map('strtolower', $country_array);
$country_array = array_map('ucwords', $country_array);
$country_menu = select_opt($country_array, $mbrstr['country']);

$mbrsite_cat_menu = select_opt($webcategory_array, $mbrstr['mbrsite_cat']);

$optinmearr = array(0, 1);
$optinme_cek = radiobox_opt($optinmearr, $mbrstr['optinme']);
$showsitearr = array(0, 1);
$showsite_cek = radiobox_opt($showsitearr, $mbrstr['showsite']);

$mbrimgstr = ($mbrstr['mbr_image']) ? $mbrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];

if (isset($_FILES['mbr_image']) && $_FILES['mbr_image']["tmp_name"] != '') {
    // process images
    $mbr_image = do_imgresize('mbr_image_' . $mbrstr['id'], $_FILES["mbr_image"]["tmp_name"], $cfgrow['mbrmax_image_width'], $cfgrow['mbrmax_image_height'], 'jpeg');
    $data = array(
        'mbr_image' => $mbr_image,
    );

    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));
    if ($update) {
        $_SESSION['dotoaster'] = "toastr.success('Profile picture updated successfully!', 'Success');";
    }
    redirpageto('index.php?hal=' . $hal);
    exit;
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    // if new email exist, check aadhar card and pan card 

    $condition = ' AND adhar_card LIKE "' . $FORM['adhar_card'] . '" LIMIT 1';
    // echo $myquery = "SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "";
    $Checksql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");
    // var_dump($Checksql[0]['adhar_card']);exit;
    $checkAdhar = '';
    $checkPanCard = '';



    $mbr_sosmed = put_optionvals($mbr_sosmed, 'mbr_twitter', mystriptag($mbr_twitter));
    $mbr_sosmed = put_optionvals($mbr_sosmed, 'mbr_facebook', mystriptag($mbr_facebook));

    // if password change
    if ($password1 == $password2 && $ischangeok == 1) {
        $data = array(
            'password' => getpasshash($password1),
        );
        $update0 = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));
        $_SESSION['dotoaster'] = "toastr.success('Password updated successfully!', 'Success');";
        redirpageto('index.php?hal=' . $hal);
        exit;
    }

    // if ($checkAdhar!='null' || $checkAdhar=='match') {
    //     // do nothing
    //     $_SESSION['dotoaster'] = "toastr.warning('<strong>Existed Account with same Adhar Card!</strong>', 'Warning');";
    //      redirpageto('index.php?hal=' . $hal);
    //     exit;
    // }else{
    if ($mbrstr['mbrstatus'] > 1) {
        $_SESSION['dotoaster'] = "toastr.error('You did not change anything!', 'Account limited');";
        redirpageto('index.php?hal=' . $hal);
        exit;
    }


    $imgtofile = getwebssdata($mbrstr, $mbrsite_url);
    if ($mbrsite_url != $mbrsite_url_old && $imgtofile != '') {
        $mbrsite_img = ".." . $imgtofile;
    } else {
        $mbrsite_img = $mbrsite_img_old;
    }

    $mbr_intro = mystriptag($mbr_intro);

    $data = array(
        'firstname' => mystriptag($firstname),
        'lastname' => mystriptag($lastname),
        'adhar_card' => mystriptag($adhar_card),
        'pan_card' => mystriptag($pan_card),
        'email' => mystriptag($email, 'email'),
        'optinme' => $optinme,
        'mbr_intro' => base64_encode($mbr_intro),
        'address' => mystriptag($address),
        'state' => mystriptag($state),
        'country' => $country,
        'phone' => mystriptag($phone),
        'altphone' => mystriptag($altphone),
        'mbr_sosmed' => mystriptag($mbr_sosmed),
        'mbrsite_url' => mystriptag($mbrsite_url, 'url'),
        'mbrsite_title' => substr(mystriptag($mbrsite_title), 0, $cfgrow['mbrmax_title_char']),
        'mbrsite_desc' => base64_encode(mystriptag(substr($mbrsite_desc, 0, $cfgrow['mbrmax_descr_char']))),
        'mbrsite_cat' => $mbrsite_cat,
        'mbrsite_img' => $mbrsite_img,
        'showsite' => $showsite,
        'bankname' => base64_encode(mystriptag($bankname)),
        'accountnum' => base64_encode(mystriptag($accountnum)),
        'bankifsc' => base64_encode(mystriptag($bankifsc)),
    );

    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));

    // ---

    if ($update0 || $update || $update1 || $insert) {
        $_SESSION['dotoaster'] = "toastr.success('Record updated successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
    }

    redirpageto('index.php?hal=' . $hal);
    exit;
}


$faiconcolor = ($mbrstr['mbrstatus'] == 2) ? '<div class="section-header-breadcrumb"><i class="fa fa-2x fa-fw fa-lock text-danger"></i></div>' : '';
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-user-cog"></i> <?php echo myvalidate($LANG['m_profilecfg']); ?></h1>
    <?php echo myvalidate($faiconcolor); ?>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h4><?php echo myvalidate($mbrstr['username']); ?></h4>
                </div>
                <div class="card-body">
                    <form enctype="multipart/form-data" method="post" role="form" onsubmit="verify()" id="update_mbr_image">
                        <input type="hidden" name="hal" value="accountcfg">
                        <div class="text-center">
                            <img id="mbr_image_btn" width='<?php echo myvalidate($cfgrow['mbrmax_image_width']); ?>' height='<?php echo myvalidate($cfgrow['mbrmax_image_height']); ?>' alt="<?php echo myvalidate($mbrstr['username']); ?>" src="<?php echo myvalidate($mbrimgstr); ?>" class="img-fluid rounded-circle img-thumbnail img-shadow author-box-picture" style="cursor: pointer;">
                            <input type="file" id="my_file" name="mbr_image" style="display: none;" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Settings</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="config-tab1" data-toggle="tab" href="#cfgtab1" role="tab" aria-controls="profile" aria-selected="true">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab2" data-toggle="tab" href="#cfgtab2" role="tab" aria-controls="account" aria-selected="false">Account</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" id="config-tab3" data-toggle="tab" href="#cfgtab3" role="tab" aria-controls="website" aria-selected="false">Website</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab4" data-toggle="tab" href="#cfgtab4" role="tab" aria-controls="password" aria-selected="false">Password</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">

                <form method="post" action="index.php" id="cfgform" oninput='password1.setCustomValidity(password2.value != password1.value ? "Passwords do not match." : "")' onsubmit="verify()">
                    <input type="hidden" name="hal" value="accountcfg">

                    <div class="card-header">
                        <h4>Options</h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">

                            <div class="tab-pane fade show active" id="cfgtab1" role="tabpanel" aria-labelledby="config-tab1">
                                <p class="text-muted"><?php echo myvalidate($LANG['m_profileaccnote']); ?></p>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label><?php echo myvalidate($LANG['g_firstname']); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($mbrstr['firstname']) ? $mbrstr['firstname'] : ''; ?>" placeholder="Member first name" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><?php echo myvalidate($LANG['g_lastname']); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($mbrstr['lastname']) ? $mbrstr['lastname'] : ''; ?>" placeholder="Member last name" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><?php echo myvalidate($LANG['g_adharecard']); ?> <span class="text-danger">*</span></label>
                                        <input type="text" maxlength="12" name="adhar_card" id="adhar_card" class="form-control" value="<?php echo isset($mbrstr['adhar_card']) ? $mbrstr['adhar_card'] : ''; ?>" placeholder="Aahar Card Number" required>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><?php echo myvalidate($LANG['g_pancard']); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="pan_card" id="pan_card" maxlength="10" class="form-control" value="<?php echo isset($mbrstr['pan_card']) ? $mbrstr['pan_card'] : ''; ?>" placeholder="Pan Card Number" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <?php if ($mbrstr['confirm_email'] == 0) { ?>
                                            <span class="text-danger">Unverified</span>
                                        <?php } ?>
                                        <label>Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                                            </div>
                                            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($mbrstr['email']) ? $mbrstr['email'] : ''; ?>" placeholder="Member email" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="selectgroup-pills">Opt-in for Notifications</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="optinme" value="0" class="selectgroup-input" <?php echo myvalidate($optinme_cek[0]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> No</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="optinme" value="1" class="selectgroup-input" <?php echo myvalidate($optinme_cek[1]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Yes</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>About Me</label>
                                    <textarea name="mbr_intro" class="form-control rowsize-md" id="mbr_intro" placeholder="Member profile"><?php echo isset($strmbr_intro) ? $strmbr_intro : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control rowsize-sm" id="address" rows="16" placeholder="Member address"><?php echo isset($mbrstr['address']) ? $mbrstr['address'] : ''; ?></textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>State or Province</label>
                                        <input type="text" name="state" id="state" class="form-control" value="<?php echo isset($mbrstr['state']) ? $mbrstr['state'] : ''; ?>" placeholder="Member state or province">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Country</label>
                                        <select name="country" id="country" class="form-control">
                                            <?php echo myvalidate($country_menu); ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Phone <span class="text-danger">*</span>
                                            <input type="tel" class="tel form-control" name="phone" id="phone" x-autocompletetype="tel" value="<?php echo isset($mbrstr['phone']) ? $mbrstr['phone'] : ''; ?>" placeholder="Member phone" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Alternative Phone <span class="text-danger"></span>
                                            <input type="tel" class="tel form-control" name="altphone" id="altphone" x-autocompletetype="tel" value="<?php echo isset($mbrstr['altphone']) ? $mbrstr['altphone'] : ''; ?>" placeholder="Alternative phone no">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <input type="hidden" name="mbr_sosmed" value="<?php echo isset($mbrstr['mbr_sosmed']) ? $mbrstr['mbr_sosmed'] : ''; ?>">
                                    <div class="form-group col-md-6">
                                        <label>Twitter</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fab fa-fw fa-twitter"></i></div>
                                            </div>
                                            <input type="text" name="mbr_twitter" id="mbr_twitter" class="form-control" value="<?php echo isset($mbrstr['mbr_twitter']) ? $mbrstr['mbr_twitter'] : ''; ?>" placeholder="Member twitter account">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Facebook</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fab fa-fw fa-facebook-f"></i></div>
                                            </div>
                                            <input type="text" name="mbr_facebook" id="mbr_facebook" class="form-control" value="<?php echo isset($mbrstr['mbr_facebook']) ? $mbrstr['mbr_facebook'] : ''; ?>" placeholder="Member facebook account">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="cfgtab2" role="tabpanel" aria-labelledby="config-tab2">
                                <p class="text-muted"><?php echo myvalidate($LANG['m_profilepaynote']); ?></p>     
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Bank Name</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text"><i class="fa fa-fw fa-university"></i></div>
                                                </div>
                                                <input type="text" name="bankname" id="bankname" class="form-control" 
                                                value="<?php echo base64_decode($mbrstr['bankname']);?>" 
                                                placeholder="Member Bank Name">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Account number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text"><i class="fa fa-fw fa-money-check"></i></div>
                                                </div>
                                                <input type="text" name="accountnum" id="accountnum" class="form-control" 
                                                value="<?php
                                                    echo (isset($mbrstr['accountnum']) && !empty($mbrstr['accountnum'])) ?
                                                    base64_decode($mbrstr['accountnum']) : '';
                                                ?>" 
                                                placeholder="Member Account Number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                     
                                        <div class="form-group col-md-6">
                                            <label>IFSC Code</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text"><i class="fa fa-fw fa-info"></i></div>
                                                </div>
                                                <input type="text" name="bankifsc" id="bankifsc" class="form-control" 
                                                value="<?php
                                                echo (isset($mbrstr['bankifsc']) && !empty($mbrstr['bankifsc'])) ?
                                                    base64_decode($mbrstr['bankifsc']) : '';
                                                ?>" 
                                                placeholder="Member IFSC Code">
                                            </div>
                                        </div>

                                    </div>

                            </div>


                            <div class="tab-pane fade" id="cfgtab4" role="tabpanel" aria-labelledby="config-tab4">
                                <p class="text-muted"><?php echo myvalidate($LANG['m_profilepassnote']); ?></p>

                                <div class="form-row">
                                    <input type="hidden" name="password" value="<?php echo isset($mbrstr['password']) ? $mbrstr['password'] : ''; ?>">
                                    <div class="form-group col-md-6">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="password2" id="password2" value="" placeholder="Member password">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Password Confirm</label>
                                        <input type="password" class="form-control" name="password1" id="password1" value="" placeholder="Confirm member password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input name="ischangeok" value="1" type="checkbox" class="custom-control-input" id="ischangeok">
                                        <label class="custom-control-label" for="ischangeok"><?php echo myvalidate($LANG['m_confirmpass']); ?></label>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> Reset
                        </button>
                        <button type="submit" name="submit" onclick="verify()" value="submit" id="submit" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                        </button>
                        <input type="hidden" name="dosubmit" value="1">
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script language="JavaScript" type="text/javascript">
    $(function() {
        $("input[name='adhar_card']").on('input', function(e) {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });
    });

    $(document).ready(function() {
        $("#mbr_image_btn").on("click", function() {
            $("#my_file").click();
        });
        $("#my_file").on("change", function() {
            //alert('=> ' + $("#my_file").val());
            //$("form").submit();
            $("#update_mbr_image")[0].submit();
        });

    });

    function verify() {
        let adhar_card = $('#adhar_card').val();
        let pan_card = $('#pan_card').val();
        let phone_number = $('#phone').val();

        if (adhar_card == '' && pan_card == '' && phone_number == '') {
            alert("Please Complete your KYC before any update on your profile");
            return false;
        }
    }
</script>