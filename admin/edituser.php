<?php
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$editId = intval($FORM['editId']);
if (isset($editId) and $editId != "") {
    // Get member details
    $rowstr = getmbrinfo($editId);

    $_SESSION['redirto'] = redir_to($FORM['redir']);

    $mbr_sosmed = get_optionvals($rowstr['mbr_sosmed']);
    $rowstr['mbr_twitter'] = $mbr_sosmed['mbr_twitter'];
    $rowstr['mbr_facebook'] = $mbr_sosmed['mbr_facebook'];

    $status_arr = array('0' => 'Inactive', '1' => 'Active', '2' => 'Limited', '3' => 'Pending');
    $status_menu = select_opt($status_arr, $rowstr['mbrstatus']);

    $mpstatus_arr = array('0' => 'Inactive', '1' => 'Active', '2' => 'Expire', '3' => 'Pending');
    $mpstatus_menu = select_opt($mpstatus_arr, $rowstr['mpstatus']);

    $country_array = array_map('strtolower', $country_array);
    $country_array = array_map('ucwords', $country_array);
    $country_menu = select_opt($country_array, $rowstr['country']);

    $mbrsite_cat_menu = select_opt($webcategory_array, $rowstr['mbrsite_cat']);

    $showsite_cek = checkbox_opt($rowstr['showsite']);
    $optinme_cek = checkbox_opt($rowstr['optinme']);
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    $editId = intval($editId);

    if (!dumbtoken($dumbtoken)) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
        $redirval = "?res=errtoken";
        redirpageto($redirval);
        exit;
    }

    $mbr_sosmed = put_optionvals($mbr_sosmed, 'mbr_twitter', mystriptag($mbr_twitter));
    $mbr_sosmed = put_optionvals($mbr_sosmed, 'mbr_facebook', mystriptag($mbr_facebook));

    // if new username exist, keep using old username
    $condition = ' AND username LIKE "' . $username . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        // do nothing
        $usernamesql = array();
        $newusernamestr = 'Username exist, ';
    } else {
        // update username
        $username = mystriptag($username, 'user');
        $usernamesql = array('username' => $username);
        $newusernamestr = 'New username, ';
    }

    // if password change
    if ($password1 == $password2 && $password1 != '') {
        $password = getpasshash($password1);
    }

    $country = ($country_array[$country]) ? $country : '';

    $data = array(
        'email' => mystriptag($email, 'email'),
        'phone' => $phone,
        'firstname' => mystriptag($firstname),
        'lastname' => mystriptag($lastname),
        'password' => $password,
        'mbrsite_url' => mystriptag($mbrsite_url, 'url'),
        'mbrsite_title' => mystriptag($mbrsite_title),
        'mbrsite_desc' => base64_encode(mystriptag($mbrsite_desc)),
        'mbrsite_cat' => $mbrsite_cat,
        'mbrsite_img' => $mbrsite_img,
        'showsite' => $showsite,
        'mbr_image' => $mbr_image,
        'mbr_intro' => base64_encode(mystriptag($mbr_intro)),
        'mbr_sosmed' => $mbr_sosmed,
        'address' => mystriptag($address),
        'state' => mystriptag($state),
        'country' => $country,
        'optinme' => $optinme,
        'taglabel' => mystriptag($taglabel),
        'mbrstatus' => $mbrstatus,
        'ewallet' => $ewallet,
        'epoint' => $epoint,
        'adminfo' => mystriptag($adminfo),
    );

    $data = array_merge($data, $usernamesql);

    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $editId));
    if ($update) {
        $_SESSION['dotoaster'] = "toastr.success('{$newusernamestr}record updated successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
    }

    // adjust wallet
    adjusttrxwallet($oldewallet, $ewallet, $editId);

    if ($mpid > 0) {
        $data = array(
            'mpstatus' => $mpstatus,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $mpid));
    }

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    header('location: ' . $redirto);
    exit;
}
?>

<div class="row">
    <div class="col-md-12">

        <p class="text-primary">Fields with <span class="text-danger">*</span> are mandatory!</p>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item text-center">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true"><span><i class="fa fa-fw fa-user-edit"></i></span><span class="d-none d-sm-block"> Profile</span></a>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="account-tab" data-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="false"><i class="fa fa-fw fa-university"></i><span class="d-none d-sm-block"> Account</span></a>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="website-tab" data-toggle="tab" href="#website" role="tab" aria-controls="website" aria-selected="false"><i class="fa fa-fw fa-globe"></i><span class="d-none d-sm-block"> Website</span></a>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="option-tab" data-toggle="tab" href="#option" role="tab" aria-controls="option" aria-selected="false"><i class="fa fa-fw fa-user-cog"></i><span class="d-none d-sm-block"> Option</span></a>
            </li>
        </ul>
        <form method="post" action="edituser.php" oninput='password1.setCustomValidity(password2.value != password1.value ? "Passwords do not match." : "")'>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo myvalidate($LANG['g_firstname']); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($rowstr['firstname']) ? $rowstr['firstname'] : ''; ?>" placeholder="Member first name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo myvalidate($LANG['g_lastname']); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($rowstr['lastname']) ? $rowstr['lastname'] : ''; ?>" placeholder="Member last name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-fw fa-user"></i></div>
                                </div>
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($rowstr['username']) ? $rowstr['username'] : ''; ?>" placeholder="Member name" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                                </div>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($rowstr['email']) ? $rowstr['email'] : ''; ?>" placeholder="Member email" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>About Member</label>
                        <textarea name="mbr_intro" class="form-control rowsize-sm" id="mbr_intro" rows="16" placeholder="Member profile"><?php echo isset($rowstr['mbr_intro']) ? base64_decode($rowstr['mbr_intro']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Member Address</label>
                        <textarea name="address" class="form-control" id="address" rows="16" placeholder="Member address"><?php echo isset($rowstr['address']) ? $rowstr['address'] : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>State or Province</label>
                            <input type="text" name="state" id="state" class="form-control" value="<?php echo isset($rowstr['state']) ? $rowstr['state'] : ''; ?>" placeholder="Member state or province">
                        </div>
                        <div class="form-group col-md-5">
                            <label>Country</label>
                            <select name="country" id="country" class="form-control">
                                <?php echo myvalidate($country_menu); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Phone</label>
                            <input type="tel" class="tel form-control" name="phone" id="phone" x-autocompletetype="tel" value="<?php echo isset($rowstr['phone']) ? $rowstr['phone'] : ''; ?>" placeholder="Member phone">
                        </div>
                    </div>

                    <div class="form-row">
                        <input type="hidden" name="mbr_sosmed" value="<?php echo isset($rowstr['mbr_sosmed']) ? $rowstr['mbr_sosmed'] : ''; ?>">
                        <div class="form-group col-md-6">
                            <label>Twitter</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fab fa-fw fa-twitter"></i></div>
                                </div>
                                <input type="text" name="mbr_twitter" id="mbr_twitter" class="form-control" value="<?php echo isset($rowstr['mbr_twitter']) ? $rowstr['mbr_twitter'] : ''; ?>" placeholder="Member twitter account">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Facebook</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fab fa-fw fa-facebook-f"></i></div>
                                </div>
                                <input type="text" name="mbr_facebook" id="mbr_facebook" class="form-control" value="<?php echo isset($rowstr['mbr_facebook']) ? $rowstr['mbr_facebook'] : ''; ?>" placeholder="Member facebook account">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
                    <div class="form-group">
                        <label>Account Status</label>
                        <select name="mbrstatus" id="mbrstatus" class="form-control">
                            <?php echo myvalidate($status_menu); ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Wallet Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-fw fa-wallet"></i></div>
                                </div>
                                <input type="text" name="ewallet" id="ewallet" class="form-control" value="<?php echo isset($rowstr['ewallet']) ? $rowstr['ewallet'] : '0'; ?>" placeholder="Member available fund">
                                <input type="hidden" name="oldewallet" value="<?php echo isset($rowstr['ewallet']) ? $rowstr['ewallet'] : '0'; ?>">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Total Point</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-fw fa-coins"></i></div>
                                </div>
                                <input type="text" name="epoint" id="epoint" class="form-control" value="<?php echo isset($rowstr['epoint']) ? $rowstr['epoint'] : ''; ?>" placeholder="Member available point">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Member Tag or Label</label>
                        <input type="text" name="taglabel" id="taglabel" class="form-control" value="<?php echo isset($rowstr['taglabel']) ? $rowstr['taglabel'] : ''; ?>" placeholder="Member tag or label">
                    </div>

                    <div class="form-group">
                        <label>Member Note</label>
                        <textarea name="adminfo" class="form-control rowsize-md" id="adminfo" rows="16" placeholder="Member note, available for administrator only"><?php echo isset($rowstr['adminfo']) ? $rowstr['adminfo'] : ''; ?></textarea>
                    </div>

                </div>

                <div class="tab-pane fade" id="website" role="tabpanel" aria-labelledby="website-tab">
                    <div class="form-group">
                        <label>Site URL</label>
                        <input type="text" name="mbrsite_url" id="mbrsite_url" class="form-control" value="<?php echo isset($rowstr['mbrsite_url']) ? $rowstr['mbrsite_url'] : ''; ?>" placeholder="Member site URL">
                    </div>

                    <div class="form-group">
                        <label>Site Title</label>
                        <input type="text" name="mbrsite_title" id="mbrsite_title" class="form-control" value="<?php echo isset($rowstr['mbrsite_title']) ? $rowstr['mbrsite_title'] : ''; ?>" placeholder="Member site title">
                    </div>

                    <div class="form-group">
                        <label>Site Description</label>
                        <textarea name="mbrsite_desc" class="form-control" id="mbrsite_desc" rows="16" placeholder="Member site description"><?php echo isset($rowstr['mbrsite_desc']) ? base64_decode($rowstr['mbrsite_desc']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Site Category</label>
                        <select name="mbrsite_cat" id="mbrsite_cat" class="form-control">
                            <?php echo myvalidate($mbrsite_cat_menu); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Site Image or Logo</label>
                        <input type="text" name="mbrsite_img" id="mbrsite_img" class="form-control" value="<?php echo isset($rowstr['mbrsite_img']) ? $rowstr['mbrsite_img'] : DEFIMG_SITE; ?>" placeholder="Member site image">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input name="showsite" value="1" type="checkbox" class="custom-control-input" id="showsite"<?php echo myvalidate($showsite_cek); ?>>
                            <label class="custom-control-label" for="showsite">Display Member Site</label>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="option" role="tabpanel" aria-labelledby="option-tab">
                    <div class="form-row">
                        <input type="hidden" name="password" value="<?php echo isset($rowstr['password']) ? $rowstr['password'] : ''; ?>">
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
                            <input name="optinme" value="1" type="checkbox" class="custom-control-input" id="optinme"<?php echo myvalidate($optinme_cek); ?>>
                            <label class="custom-control-label" for="optinme">Opt-in for Notifications</label>
                        </div>
                    </div>

                    <?php
                    if ($rowstr['mpid'] > 0) {
                        ?>
                        <hr>
                        <div class="form-group">
                            <label>Member Payplan Status</label>
                            <select name="mpstatus" id="mpstatus" class="form-control">
                                <?php echo myvalidate($mpstatus_menu); ?>
                            </select>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="text-md-right">
                    <input type="hidden" name="editId" id="editId" value="<?php echo isset($editId) ? $editId : '' ?>">
                    <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal"><i class="far fa-fw fa-times-circle"></i> Cancel</a>
                    <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                        <i class="fa fa-fw fa-undo"></i> Reset
                    </button>
                    <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary" data-dismiss="static">
                        <i class="fa fa-fw fa-edit"></i> Update
                    </button>
                    <input type="hidden" name="mpid" value="<?php echo myvalidate($rowstr['mpid']); ?>">
                    <input type="hidden" name="dosubmit" value="1">
                    <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                </div>

            </div>
        </form>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeTab<?php echo isset($editId) ? $editId : 'U' ?>', $(e.target).attr('href'));
        });

        var activeTab = localStorage.getItem('activeTab<?php echo isset($editId) ? $editId : 'U' ?>');
        if (activeTab) {
            $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>