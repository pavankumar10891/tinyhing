<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo e(Config::get('Site.title')); ?></title>
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
</head>

<body>
  <table width="100%" bgcolor="#f1f1f1">
    <tr>
      <td align="left" valign="top" cell-spacing="0" cell-padding="0">&nbsp;</td>
      <td align="left" valign="top" width="600px" style="max-width:100%;" cell-spacing="0" cell-padding="0">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"
          style="font-family:'Arial', sans-serif; background:#fff; border:1px solid #f5f5f5; margin:10px 0px"
          align="center">
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0"
                style="padding:5px 15px; background: #82a79c">
                <tr>
                <?php $logo = CustomHelper::getlogo();
                $logoimage = '';
                if($logo){
                  $logoimage = $logo->image;
                } 
                ?>
                  <td align="left" valign="middle" style="padding:5px 0px;  "><img src="<?php echo e($logoimage); ?>" alt="logo"></td>
                  <td align="center" valign="middle" style="padding:5px 0px; ">
                    <a href="<?php echo e(WEBSITE_URL); ?>" target="_blank" style="color:#000040; font-size:18px; text-decoration:none; color:#fff; font-weight:bold; text-transform:uppercase;"><?php echo e(Config::get('Site.title')); ?></a>
                  </td>
                  <td align="right" valign="middle" style="padding:5px 0px;  "></td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
                <tr>
                  <td valign="top" style="padding:25px 15px;">
                    <?php echo $messageBody; ?>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ddd" style="padding:10px 20px;">
                <tr>
                  <td valign="top" align="left" style="padding:10px 20px; text-align:center;"><span
                      style="width:100%;margin:0 0  0px 0;color:#888;font-weight:500;font-size:14px;float:left;">&copy;Copyright
                      <?php echo date("Y"); ?>. All Rights Reserved.
                    </span></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
      <td align="left" valign="top" cell-spacing="0" cell-padding="0">&nbsp;</td>
    </tr>
  </table>
</body>

</html><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/emails/template.blade.php ENDPATH**/ ?>