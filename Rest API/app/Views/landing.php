<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mividy</title>
    <link rel="stylesheet" href="<?= base_url('library/aos/aos.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?q='.$randomString); ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico'); ?>">
    <link rel="stylesheet" href="<?= base_url('library/boxicons/css/boxicons.min.css'); ?>">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <img  data-aos="fade-down" data-aos-delay="300" src="<?= base_url('favicon.ico'); ?>" title="<?= trim($appName); ?>" alt="<?= trim($appName); ?>" class="navbar-logo">
            <a  data-aos="fade-down" data-aos-delay="400" class="navbar-brand" href=""><?= trim(htmlspecialchars($appName)); ?></a>
            <button  data-aos="fade-down" data-aos-delay="500" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class='bx bx-menu'></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                  <li class="nav-item">
                    <a  data-aos="fade-down" data-aos-delay="600" class="nav-link" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a  data-aos="fade-down" data-aos-delay="700" class="nav-link" aria-current="page" href="#preview">Preview</a>
                </li>
                <li class="nav-item">
                    <a  data-aos="fade-down" data-aos-delay="800" class="nav-link" aria-current="page" href="#team">Team</a>
                </li>
                <li class="nav-item">
                    <a  data-aos="fade-down" data-aos-delay="900" class="nav-link" aria-current="page" href="<?= $downloadUrlClient ?>">Download</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- /Navbar -->

<!-- Main Container -->
<div class="head-spacer"></div>
<div class="container main">
    <section id="home">
        <div class="row" style="margin-bottom: -40px;">
            <div class="col-md-12" style="height: fit-content;">
                <div class="site-heading text-center" style="height: fit-content;">
                    <h2 data-aos="fade-left" data-aos-delay="300"><span>Home</span></h2>
                    <h4 data-aos="fade-left" data-aos-delay="400" >Selamat datang di website kami</h4>
                </div>
            </div>
        </div>
        <div  data-aos="fade-left" data-aos-delay="200" class="app-name">Mividy</div>
        <div  data-aos="fade-left" data-aos-delay="300" class="slogan">Permudahkan transaksi jual beli Anda dengan Mividy,<br>Aplikasi management transaksi aman dan nyaman bagi administrator penjualan.<br>Mividy dapat digunakan dan diakses dari berbagai penjuru wilayah.</div>
        <img data-tilt data-aos="fade-left" data-aos-delay="400" src="<?= base_url('assets/images/3d-graphics/transaksi.png'); ?>" title="Transaksi Mividy" class="home3d">
        <a  data-aos="fade-left" data-aos-delay="500" class="mouse-down" href="#preview"><i class='bx bxs-mouse-alt'></i></a>
    </section>  
    <section id="preview">
        <!-- Start Header Section -->
        <div class="row"  style="margin-bottom: -40px;">
            <div class="col-md-12">
                <div class="site-heading text-center">
                    <h2 data-aos="fade-left" data-aos-delay="300">APP <span>PREVIEW</span></h2>
                    <h4 data-aos="fade-left" data-aos-delay="400">Beberapa tangkapan layar dari aplikasi kami</h4>
                </div>
            </div>
        </div>
        <!-- / End Header Section -->
        <div class="row justify-content-center">
            <?php $animationDelay = 200; foreach($previews as $preview) : ?><?php 


            if($animationDelay == 600) {
                $animationDelay = 300;
            } else {
                $animationDelay = $animationDelay + 100;
            }

             ?>
            <!-- Single preview -->
            <div class="col-sm-6 col-lg-4 col-xl-3" data-aos="fade-left" data-aos-delay="<?= $animationDelay; ?>">
               <div class="single-preview">
                  <div class="preview-image">
                    <img src="<?= base_url('assets/images/preview/' . $preview['imageName']); ?>" title="<?= $preview['title'] ?>" data-description="<?= $preview['description']; ?>">
                </div>
            </div>
        </div>
        <!-- /Single Preview -->
    <?php endforeach; ?>
</section>
<section id="team" class="team-area">
    <div class="container">
        <div class="row"  style="margin-bottom: -40px;">
            <div class="col-md-12">
                <div class="site-heading text-center" data-aos="fade-left" data-aos-delay="200">
                    <h2>Our <span>Team</span></h2>
                    <h4>Team kami</h4>
                </div>
            </div>
        </div>
        <div class="row team-items justify-content-center">
            <?php $animationDelay = 200; foreach($teams as $team) : ?><?php 


            if($animationDelay == 600) {
                $animationDelay = 300;
            } else {
                $animationDelay = $animationDelay + 100;
            }

             ?>
                 <!-- Single Team -->
            <div class="col-md-4 single-item" data-aos="fade-left" data-aos-delay="<?= $animationDelay ?>">
                <div class="item" data-tilt>
                    <div class="thumb">
                        <img class="img-fluid" src="<?= base_url('assets/images/profile/'.$team['pictures']) ?>" alt="<?= $team['name']; ?>" title="<?= $team['name']; ?>">
                    </div>
                    <div class="info">
                        <h4><?= trim(htmlspecialchars($team['name'])); ?></h4>
                        <span><?= $team['role']; ?></span>
                        <div class="social"><?php foreach($team['social'] as $social) : 
                                $icon = "";
                                $type = $social['type'];
                                if($type == "facebook") {
                                    $icon = "<i class='bx bxl-facebook-circle'></i>";
                                } elseif($type == "whatsapp") {
                                    $icon = "<i class='bx bxl-whatsapp' ></i>";
                                } elseif($type == "linkedin") {
                                    $icon = "<i class='bx bxl-linkedin' ></i>";
                                } elseif($type == "email") {
                                    $icon = "<i class='bx bxs-envelope' ></i>";
                                } elseif($type == "github") {
                                    $icon = "<i class='bx bxl-github' ></i>";
                                } elseif($type == "instagram") {
                                    $icon = "<i class='bx bxl-instagram'></i>";
                                }
                                ?><a class="<?= $social['type']; ?>" href="<?= $social['url']; ?>" target="_blank"><?= $icon; ?></a><?php endforeach; ?></div>
                    </div>
                </div>
            </div>
            <!-- /Single Team -->
            <?php endforeach; ?>
        </div>
    </div>
</div>
</section>
</div>
<!-- /Main Container -->
<!-- Modal Preview -->
<div class="modal fade " id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalPreviewLabel"></h5>
            <button type="button" data-bs-dismiss="modal" aria-label="Close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <div class="title"></div>
            <div class="img" data-tilt><img></div>
            <div class="description"></div>
        </div>
        <div class="modal-footer">
            <button type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>
<!-- /Modal Preview -->
<!-- Fixed Bottom Notice -->
<div class="fixed-bottom-notice">
    <h5>Copyright &copy; <?= date('Y') ?> <?= trim(htmlspecialchars($appDeveloper)); ?></h5>
</div>
<!-- /Fixed Bottom Notice -->
<script type="text/javascript" src="<?= base_url('library/vanilla-tilt.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('library/aos/aos.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('library/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/javascript/muhendo.js'); ?>"></script>
</body>
</html>