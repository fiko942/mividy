<!DOCTYPE html>
<html lang="id">
<head>
   <title>Faktur #<?= $faktur['id']; ?></title>
   <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
   <link rel="apple-touch-icon" href="<?= base_url('favicon.ico'); ?>">
   <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('favicon.ico'); ?>">
   <link rel="apple-touch-icon" sizes="120x120" href="<?= base_url('favicon.ico'); ?>">
   <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('favicon.ico'); ?>">
   <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico'); ?>" />
   <link rel="stylesheet" type="text/css" href="<?= base_url('faktur/style'); ?>">
   <meta name="apple-mobile-web-app-capable" content="yes">
   <meta name="apple-touch-fullscreen" content="yes">
   <meta name="apple-mobile-web-app-status-bar-style" content="default">
   <meta content="Faktur pembayaran kepada Hadywijaya" name="description" />
   <meta content="Wiji Fiko Teren" name="author" />
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <meta name="robots" content="index,follow" />   
   <meta name="viewport" content="width=device-width; initial-scale=1.0;" />
</head>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
   <tr>
      <td height="10"></td>
   </tr>
   <tr>
      <td>
         <table width="600" border="0" style="border-top-left-radius: 20px; border-top-right-radius: 20px;" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#34495E" style="border-radius: 0 0 0 0;">
            <tr class="hiddenMobile">
               <td height="20"></td>
            </tr>
            <tr class="visibleMobile">
               <td height="10"></td>
            </tr>
            <tr>
               <td>
                  <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                     <tbody>
                        <tr>
                           <td>
                              <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                 <tbody>
                                    <tr>
                                       <td align="left"> <img src="<?= base_url('favicon.ico'); ?>" width="60" height="60" alt="logo" border="0" /></td>
                                    </tr>
                                    <tr class="hiddenMobile">
                                       <td height="20"></td>
                                    </tr>
                                    <tr class="visibleMobile">
                                       <td height="10"></td>
                                    </tr>
                                    <tr>
                                       <td style="font-size: 12px; color: #ffffff; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left; font-size: 13px;">Halo <?= trim(htmlspecialchars($faktur['nama'])); ?>, <br>Terimakasih telah melakukan pembelian.</td>
                                    </tr>
                                 </tbody>
                              </table>
                              <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                 <tbody>
                                    <tr class="visibleMobile">
                                       <td height="20"></td>
                                    </tr>
                                    <tr>
                                       <td height="5"></td>
                                    </tr>
                                    <tr>
                                       <td style="font-size: 21px; color: #ff0000; letter-spacing: -1px; font-family: 'Open Sans', sans-serif; line-height: 1; vertical-align: top; text-align: right; letter-spacing: 1px;color: #ffffff;"> Faktur #<?= trim(htmlspecialchars($faktur['id'])); ?> </td>
                                    </tr>
                                    <tr> 
                                       <tr class="hiddenMobile">
                                          <td height="30"></td>
                                       </tr>
                                       <tr class="visibleMobile">
                                          <td height="20"></td>
                                       </tr>
                                       <tr>
                                          <td style="font-size: 12px; color: #ffffff; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: right; font-size: 15px;"> <small>STATUS:</small> Telah Dibayar <br> <small>DATE: <?= trim(htmlspecialchars($faktur['tanggal'])); ?></small> </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
               </tr>
            </table>
         </td>
      </tr>
   </table>
   <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#34495E">
                  <tbody>
                     <tr> 
                        <tr class="hiddenMobile">
                           <td height="20"></td>
                        </tr>
                        <tr class="visibleMobile">
                           <td height="10"></td>
                        </tr>
                        <tr>
                           <td>
                              <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                 <tbody>
                                    <tr>
                                       <th style="font-size: 14px; letter-spacing: 1px; font-family: 'Open Sans', sans-serif; color: #ffffff; font-weight: 500; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;" width="52%" align="left"> Item </th>
                                       <th style="font-size: 14px;letter-spacing: 1px; font-family: 'Open Sans', sans-serif; color: #ffffff; font-weight: 500; line-height: 1; vertical-align: top; padding: 0 0 3px;" align="right"> Harga </th>
                                    </tr>
                                    <tr>
                                       <td height="1" style="background: #17B3B4;" colspan="4"></td>
                                    </tr>
                                    <tr>
                                       <td height="10" colspan="4"></td>
                                    </tr>
                                    <tr>
                                       <td style="font-size: 13px; font-family: 'Open Sans', sans-serif; color: #ff0000; line-height: 18px; vertical-align: top; padding:10px 0; padding-top: 0; color: #ffffff; font-weight: 500; font-size: 17px;" class="article"> <?= trim(htmlspecialchars($faktur['barang'])); ?> </td>
                                       <td style="font-size: 13px; font-family: 'Open Sans', sans-serif; color: #ffffff; line-height: 18px; vertical-align: top; padding:10px 0; padding-top: 0; font-weight: 500; font-size: 17px; font-weight: 500;" align="right"><?php

                                       function formatRupiah(int $nominal){
                                          return "Rp " . number_format($nominal, 0, ',', '.');
                                       }

                                       echo formatRupiah($faktur['nominal']);

                                    ?></td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td height="10"></td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <!-- /Order Details --> <!-- Total --> 
   <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#34495E">
                  <tbody>
                     <tr>
                        <td>
                           <!-- Table Total --> 
                           <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                              <tbody>
                                 <tr>
                                    <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #ffffff; line-height: 22px; vertical-align: top; text-align:right; font-weight: 500; font-size: 15px; "> <strong>Jumlah: <?= formatRupiah($faktur['nominal']); ?></strong> </td>
                                 </tr>
                              </tbody>
                           </table>
                           <!-- /Table Total --> 
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <!-- /Total --> <!-- Information --> 
   <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#34495E">
                  <tbody>
                     <tr> 
                        <tr class="hiddenMobile">
                           <td height="10"></td>
                        </tr>
                        <tr class="visibleMobile">
                           <td height="10"></td>
                        </tr>
                        <tr>
                           <td>
                              <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                 <tbody>
                                    <tr>
                                       <td>
                                          <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                             <tbody>
                                                <tr class="hiddenMobile">
                                                   <td height="35"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                   <td height="20"></td>
                                                </tr>
                                                <tr>
                                                   <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #ffffff; line-height: 1; vertical-align: top; font-size: 13px; font-weight: 500;"> <strong>TAGIHAN KEPADA</strong> </td>
                                                </tr>
                                                <tr>
                                                   <td width="100%" height="6"></td>
                                                </tr>
                                                <tr>
                                                   <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #ffffff; line-height: 20px; vertical-align: top; font-weight: 400; font-size: 14px; letter-spacing: 1px;"> <?= trim(htmlspecialchars($faktur['nama'])) ?></td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col" style="width: fit-content; margin-left: auto; margin-right: 0; float: right;">
                                             <tbody style="width: fit-content; margin-left: auto; margin-right: 0; float: right;">
                                                <tr class="hiddenMobile">
                                                   <td height="35"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                   <td height="20"></td>
                                                </tr>
                                                <tr>
                                                   <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #ffffff; line-height: 1; vertical-align: top; width: fit-content; margin-left: auto; margin-right: 0; float: right; font-size: 13px; font-weight: 500;"> <strong>DIBAYARKAN KEPADA</strong> </td>
                                                </tr>
                                                <tr>
                                                   <td width="100%" height="6"></td>
                                                </tr>
                                                <tr>
                                                   <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #ffffff; line-height: 20px; vertical-align: top; font-weight: 400; font-weight: 13px; letter-spacing: 1px;"><?= trim(htmlspecialchars($namaAplikasi . ' - ' . $developerAplikasi)) ?></td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr class="hiddenMobile">
                           <td height="30"></td>
                        </tr>
                        <tr class="visibleMobile">
                           <td height="15"></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      <!-- /Information --> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
         <tr>
            <td>
               <table style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;"  width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#34495E" style="border-radius: 0 0 0 0;">
                  <tr>
                     <td>
                        <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                           <tbody>
                              <tr>
                                 <td style="font-size: 12px; color: #ffffff; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left; font-size: 13px;"> Terimakasih dan salam hormat,<br><?= trim(htmlspecialchars($faktur['ditambahkan_oleh'])) ?> - <?= trim(htmlspecialchars($namaAplikasi)); ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr class="spacer">
                     <td height="30"></td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td height="20"></td>
         </tr>
      </table>