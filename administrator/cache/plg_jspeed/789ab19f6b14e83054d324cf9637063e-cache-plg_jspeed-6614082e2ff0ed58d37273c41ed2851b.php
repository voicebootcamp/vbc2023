<?php die("Access Denied"); ?>#x#a:2:{s:6:"result";s:18682:"@charset "UTF-8";
@import url(https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Work+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap);

body {
  font-family: 'Inter', sans-serif;
  color: #707179;
  line-height: 1.6;
  font-size: 16px;
  font-weight: 400;
}

a {
  text-decoration: none;
  cursor: pointer;
}

h1,
h2,
h3,
h4,
h5,
h6 {
  font-family: "Work Sans", sans-serif;
  color: #0f1235;
  margin-top: 0;
margin-bottom: .5rem;
  line-height: 1.2;
  font-weight: 700;
}

a {
  color: inherit;
  text-decoration: none;
  transition: 0.5s;
}

a,
a:hover,
a:focus,
a:active {
  text-decoration: none;
  outline: none;
  color: inherit;
}

a:hover {
  color: var(--maincolor);
}

pre {
  word-break: break-word;
}

img {
  max-width: 100%;
}

ol {
  counter-reset: counter;
  padding-left: 0;
}

dl, ol, ul {
	margin-top: 0;
	margin-bottom: 0;
	padding: 0;
}

button:hover,
button:active,
button:focus {
  outline: 0;
}

/*input and button type focus outline disable*/
input[type=text]:focus,
input[type=email]:focus,
input[type=url]:focus,
input[type=password]:focus,
input[type=search]:focus,
input[type=tel]:focus,
input[type=number]:focus,
textarea:focus,
input[type=button]:focus,
input[type=reset]:focus,
input[type=submit]:focus,
select:focus {
  outline: none;
  box-shadow: none;
  border: 1px solid #ddd;
}

h1 a,
h2 a,
h3 a,
h4 a,
h5 a,
h6 a {
  color: inherit;
}


body .sppb-btn,
body .btn {
  height: 55px;
  line-height: 55px;
  padding: 0 34px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  border: 0;
  font-size: 16px;
  transition: all 0.5s ease;
  font-weight: 400;
  z-index: 0;
  white-space: normal;
}

body .sppb-btn:after,
body .btn:after {
  content: "";
  background: var(--maincolor);
  position: absolute;
  transition: all 0.3s ease-in;
  z-index: -1;
  height: 100%;
  left: -35%;
  top: 0;
  transform: skew(-35deg);
  transition-duration: 0.5s;
  transform-origin: right top;
  width: 0;
}

body .sppb-btn:hover:after,
body .btn:hover:after {
  height: 100%;
  width: 200%;
}

body .sppb-btn.sppb-btn-primary {
  color: #fff;
  background: var(--secondcolor);
}

body .sppb-btn.sppb-btn-primary:hover {
	color: #fff;
}

body .sppb-btn.sppb-btn-primary:after,
body .btn.btn-primary:after {
  background: var(--maincolor);
}

body .sppb-btn.sppb-btn-secondary {
  color: #fff;
  background: var(--maincolor);
}

body .sppb-btn.sppb-btn-secondary:hover {
  color: #fff;
}

body .sppb-btn.sppb-btn-secondary:after,
body .btn.btn-secondary:after {
  background: var(--secondcolor);
}

button {cursor: pointer;}

.sppb-addon-optin-forms,
.sppb-addon-optin-forms .sppb-optin-form-details-wrap:not(:empty){
	padding: 0px;
}

#sp-footer {
	font-size: inherit;
}

#sp-footer #sp-footer2 {
	text-align: inherit;
}

.p-relative {
	position: relative;
}

textarea.sppb-form-control {height: auto !important; padding-top: 15px !important;}

.sp-scroll-up {
	background: var(--maincolor);
	animation: backto-top-bounce 4s infinite ease-in-out;
}

@keyframes backto-top-bounce {
  0% {
    transform: translateY(-5px);
  }
  50% {
    transform: translateY(10px);
  }
  100% {
    transform: translateY(-5px);
  }
}

.overflow-hidden {overflow: hidden;}

.z-index2 {z-index: 2 !important;}

.sppb-media-heading {
	margin: 0;
}



/* HEADER */ 

#sp-header {
	height: auto;
	box-shadow: none;
	;
	;
	;
	;
	transition: all 0.4s;
	padding: 0;
	background: transparent;
	box-shadow: none;
}

#sp-header.header-sticky {
	background: #fff;
	;
	animation: 900ms ease-in-out 0s normal none 1 running fadeInDown;
	box-shadow: 0 10px 20px 0 rgba(46, 56, 220, 0.05);
}

@-webkit-keyframes fadeInDown {
  0% {
    opacity: 0;
    ;
    transform: translateY(-100px);
  }

  100% {
    opacity: 1;
    ;
    transform: translateY(0);
  }
}

@keyframes fadeInDown {
  0% {
    opacity: 0;
    ;
    transform: translateY(-100px);
  }

  100% {
    opacity: 1;
    ;
    transform: translateY(0);
  }
}

#sp-header > .row {
	;
	align-items: center !important;
}

#sp-header > .container > .container-inner > .row {
	;
	align-items: center !important;
}

#sp-header .logo {
	height: auto;
	display: block;
}

#sp-header .logo a .logo-image,
#sp-header .logo a .logo-image-phone {
	display: inline-block;
	transition: all 0.3s ease-in-out 0s;
	height: auto;
	width: 100%;
	max-width: 170px;
}

.sp-megamenu-parent {
	margin: 0;
	float: none;
}

.sp-megamenu-parent > li > a, .sp-megamenu-parent > li > span,
.sp-megamenu-parent > li:last-child > a {
display: block;
font-size: 16px;
font-weight: 600;
padding: 0 26px;
line-height: 85px;
color: #0f1235;
  transition: all 0.5s ease;
  position: relative;
  margin-right: 1px;
  letter-spacing: .3px;
}

.sp-megamenu-parent > li.active > a,
.sp-megamenu-parent > li:hover > a, 
.sp-megamenu-parent > li.active:hover > a {
	font-weight: 600;
	color: var(--maincolor);
}

body.ltr .sp-megamenu-parent > li.sp-has-child > a::after, body.ltr .sp-megamenu-parent > li.sp-has-child > span::after {
	font-family: "Font Awesome 5 Free";
	font-weight: 900;
	content: "\f067";
	float: right;
	margin-left: 5px;
	font-size: 12px;
  transition: all 0.5s ease;	
}

body.ltr .sp-megamenu-parent > li.sp-has-child:hover > a::after, body.ltr .sp-megamenu-parent > li.sp-has-child:hover > span::after,
body.ltr .sp-megamenu-parent > li.sp-has-child.active > a::after, body.ltr .sp-megamenu-parent > li.sp-has-child.active > span::after {
	font-family: "Font Awesome 5 Free";
	font-weight: 900;
	content: "\f068";
	float: right;
	margin-left: 5px;
	font-size: 12px;
  transition: all 0.5s ease;	
}

@media(max-width: 1400px) {
.sp-megamenu-parent > li > a, .sp-megamenu-parent > li > span,
.sp-megamenu-parent > li:last-child > a {
padding: 0 22px;
}	
}

@media(max-width: 1200px) {
.sp-megamenu-parent > li > a, .sp-megamenu-parent > li > span,
.sp-megamenu-parent > li:last-child > a {
padding: 0 14px;
}	
}

.sp-megamenu-parent .sp-dropdown .sp-dropdown-inner {
	border: 0px solid transparent;
	border-radius: 0 0 12px 12px;	
	box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.05);
	transition: all 0.3s ease 0s;
	padding: 0;
	position: relative;
	overflow: hidden;
	background: transparent;
}

.sp-megamenu-parent .sp-dropdown .sp-dropdown-items {
	background: #fff;
}

.sp-megamenu-parent .sp-dropdown li.sp-menu-item {
	display: block;
	margin: 0px;
	padding: 0;
}

.sp-megamenu-parent .sp-dropdown li.sp-menu-item + li.sp-menu-item {border-top: 1px solid #f5f5f5;}

.sp-megamenu-parent .sp-dropdown li.sp-menu-item > a, .sp-megamenu-parent .sp-dropdown li.sp-menu-item span:not(.sp-menu-badge) {
    font-size: 14px;
	font-weight: 500;
	color: #050a30;
	display: block;
	padding: 10px 20px;
	position: relative;
	z-index: 1;
	line-height: 26px;
}

.sp-megamenu-parent .sp-dropdown li.sp-menu-item > a:before {
	position: absolute;
	content: "";
	top: 0;
	left: auto;
	right: 0;
	width: 0;
	height: 100%;
	background: var(--maincolor);
	z-index: -1;
}

.sp-megamenu-parent .sp-dropdown li.sp-menu-item:hover > a {
	color: #ffffff;
	padding: 10px 20px 10px 30px;
}

.sp-megamenu-parent .sp-dropdown li.sp-menu-item > a:hover:before {
	left: 0;
	right: auto;
	width: 100%;
}

#offcanvas-toggler {
	display: block;
	height: 80px;
	line-height: inherit;
	font-size: 20px;
	transition: all 0.3s ease-in-out 0s;
}

#offcanvas-toggler > .fa {
	transition: all 0.3s ease-in-out 0s;
	color: #fff;
	font-size: 32px;
}

/* */



/* PAGE TITLE */

.sp-page-title {
	padding: 150px 0 150px 0;
	text-align: center;
	background-position: 0;
}

.sp-page-title .container {
	position: relative;
	z-index: 2;
}

.sp-page-title .sp-page-title-heading {
	font-size: 48px;
	font-weight: 700;
	margin-bottom: 30px;
	color: #fff;
	line-height: 1.3380952381;
}

.sp-page-title .breadcrumb {
list-style: none;
padding: 0;
margin: 0;
background: rgba(255, 255, 255, 0.1);
padding: 5px 25px;
border-radius: 40px;
display: inline-block;
}

.sp-page-title .breadcrumb li:before {display: none;}

.sp-page-title .breadcrumb > li {
	display: inline-block;
	margin-bottom: 0;
}


.sp-page-title .breadcrumb > li > a {
	font-size: 16px;
	font-weight: 400;
	color: #fff;
	position: relative;
	margin-right: 9px;
	padding-right: 11px;
}

.sp-page-title .breadcrumb > li > a:after {
	position: absolute;
	right: 0;
	top: -1px;
	content: "/";
	height: 15px;
	width: 1px;
}

.sp-page-title .breadcrumb > li > a:hover {
	color: var(--secondcolor);
}

.sp-page-title .breadcrumb > .active {
	font-size: 16px;
	font-weight: 400;
	color: var(--secondcolor);
}

/* */

/* USERS */

.form-validate {
    background: #F6F6F6;
    border-top: 4px solid #F6F6F6;
	padding: 50px 40px;
}

.spacer, legend {display: none;}

.form-validate .form-control {
border: 0 !important;
height: 50px;
padding: 0 18px;
font-size: 14px;
border-radius: 4px;
}


/* */


#sp-header.header-sticky ~ section#sp-main-body {
	padding-top: 0;
}
.com-content #sp-main-body,
.com-j2store #sp-main-body,
.com-tags #sp-main-body,
.com-users #sp-main-body {
	padding: 120px 0;
}

.com-spsimpleportfolio #sp-main-body,
.com-spsimpleportfolio #sp-header.header-sticky ~ section#sp-main-body {
	padding: 0;
}

.com-spsimpleportfolio #sp-main-body {
	z-index: 2;
	position: relative;
}

.com-spsimpleportfolio .page-content > .sppb-section {
    padding-top: 95px;
}


.com-content #sp-header.header-sticky ~ section#sp-main-body,
.com-j2store #sp-header.header-sticky ~ section#sp-main-body,
.com-tags #sp-header.header-sticky ~ section#sp-main-body,
.com-users #sp-header.header-sticky ~ section#sp-main-body {
	padding-top: 120px;
}

/* SIDEBAR */

.sidebar-class  .sppb-row-container {width: 100% !important; }
.sidebar-class.com-sppagebuilder #sp-main-body {margin: 0 auto;}
@media (min-width: 768px) {
.sidebar-class.com-sppagebuilder #sp-main-body {max-width: 750px;}
}
@media (min-width: 992px) {
.sidebar-class.com-sppagebuilder #sp-main-body {max-width: 970px;}
}
@media (min-width: 1200px) {
.sidebar-class.com-sppagebuilder #sp-main-body {max-width: 1170px;}
}

.com-sppagebuilder #sp-right, .com-sppagebuilder #sp-left {
	padding-top: 120px;
	padding-bottom: 120px;
	position: relative;
}

#sp-left .sp-module, #sp-right .sp-module {
	border: 0px solid #f3f3f3;
	padding: 0;
	border-radius: 0px;
}

#sp-left .sp-module ul > li, #sp-right .sp-module ul > li {
	display: block;
	border-bottom: 0px solid #f3f3f3;
}

/* */


/* BLOG */

.article-list .article {
	margin-bottom: 60px;
	padding: 0px;
	border: 0px solid #f5f5f5;
	border-radius: 0px;
}

.article-list .article .article-intro-image, .article-list .article .article-featured-video, .article-list .article .article-featured-audio, .article-list .article .article-feature-gallery {
	margin: 0px;
	border-radius: 0;
	border-bottom: 0px solid #f5f5f5;
	position: relative;
	overflow: hidden;
	border-radius: 7px;
	margin-bottom: 30px;
}

.article-list .article .article-intro-image img, .article-list .article .article-featured-video img, .article-list .article .article-featured-audio img, .article-list .article .article-feature-gallery img {
	border-radius: 0;
	width: 100%;
	transform: scale(1);
	transition: 0.9s;
}

.article-list .article:hover img {
	transform: rotate(1deg) scale(1.05);
}

.article-list .article .blog-meta,
.article-details .blog-meta {
    margin-bottom: 15px;
}

.article-list .article .comnt,
.article-details .comnt {
	background: var(--maincolor);
	display: inline-block;
list-style: none;
margin-right: 15px;
    height: 28px;
    color: #fff;
    line-height: 28px;
    padding: 0 12px;
    font-size: 12px;
    margin-top: 0;
    border-top: 0;
    border-radius: 4px;
}

.article-list .article .author,
.article-details .author {
    display: inline-block;
    list-style: none;
    margin-right: 15px;
    color: #949494;
	font-weight: 500;
}

.article-list .article .author span,
.article-details .author span {
    color: #29303b;
}

.article-list .article .date,
.article-details .date {
	display: inline-block;
	list-style: none;
	margin-right: 0;
	color: #949494;
}

.article-list .article .article-header h2 {
	font-size: 24px;
	color: #29303b;
	line-height: 1.3380952381;
	margin-bottom: 15px;
}

.article-list .article .article-header h2 a {
    color: #29303b;
}

.article-list .article .article-header h1 a:hover, .article-list .article .article-header h1 a:active, .article-list .article .article-header h1 a:focus, .article-list .article .article-header h2 a:hover, .article-list .article .article-header h2 a:active, .article-list .article .article-header h2 a:focus,
.postbox__meta span a:hover {
	color: var(--maincolor);
}

.article-introtext {
    margin-bottom: 18px;
}

.article-list .article .readmore-text a {
	position: relative;
	display: inline-block;
}

.article-list .article .readmore-text a:after {
	content: "";
	position: absolute;
	left: 0;
	bottom: 0;
	height: 1px;
	width: 60%;
	background: var(--maincolor);
	transition: 0.4s;
}

.article-list .article .readmore-text a:hover:after {
	width: 100%;
}


/* SINGLE ARTICLE */

.article-details .sppb-row-container {padding: 0;}

.view-article #sp-main-body > .container {
	max-width: 100%;
	padding: 0;
}

.article-details .article-full-image {
position: relative;
overflow: hidden;
border-radius: 7px;
margin-bottom: 30px;
}

.article-details .article-full-image img {
	display: inline-block;
	border-radius: 0;
	transform: scale(1);
	transition: 0.9s;
}

.article-details:hover .article-full-image img {
    transform: rotate(1deg) scale(1.05);
}

.article-details .article-header h1, .article-details .article-header h2 {
	font-size: 60px;
	color: #0f1235;
	margin-bottom: 30px;
	text-align: center;
}

.article-details > div > p {
	text-align: center;
}

.article-details .article-author-information {
	padding-top: 2rem;
	margin-top: 75px;
	border-top: 0px solid #f5f5f5;
	padding: 40px 65px;
	;
	;
	border-radius: 4px;
	background: #f2f7ff;
}

.postbox__share {margin-top: 75px;}

#article-comments {
	padding-top: 0;
	margin-top: 75px;
	border-top: 0px solid #f5f5f5;
}

.article-social-share .social-share-icon ul li a {
	border: none;
	font-size: 15px;
	text-align: center;
	display: inline-block;
	width: 45px;
	height: 45px;
	line-height: 45px;
	text-align: center;
	background: #f2f7ff;
	color: #9499ae;
	;
	;
	border-radius: 50%;
}

.article-social-share .social-share-icon ul li a:hover {
	color: #ffffff;
	background: var(--maincolor);
}

.single-course .article-full-image, .single-course .article-details .article-can-edit,
.single-course .article-details .article-author-information, .single-course .share-rating, .single-event .article-details .article-can-edit,
.single-event .article-details .article-author-information, .single-event .share-rating {display: none;}

.single-event .article-details  {
	display: flex;
	flex-direction: column;
}

.single-event .article-details .blog-meta {order: 1;}
.single-event .article-details .article-header {order: 2;}
.single-event .article-details .article-full-image {
	order: 3;
	margin-bottom: 0;
	margin-top: 1.5rem;
}
.single-event .article-details .body-main-text {order: 4;}

/* */

/* ANIMATIONS */


@keyframes hero-image-animation {
  0% {
    transform: translateY(-30px);
  }
  100% {
    transform: translateY(0px);
  }
}

/* */


@media (min-width: 320px) {#sp-footer  .sppb-container-inner {max-width: 400px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 576px) {#sp-footer  .sppb-container-inner {max-width: 540px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 768px) {#sp-footer  .sppb-container-inner {max-width: 720px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 992px) {#sp-footer .sppb-container-inner {max-width: 960px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 1200px) {#sp-footer  .sppb-container-inner {max-width: 1140px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}

@media (min-width: 320px) {#sp-top1  .sppb-container-inner {max-width: 400px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 576px) {#sp-top1  .sppb-container-inner {max-width: 540px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 768px) {#sp-top1  .sppb-container-inner {max-width: 720px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 992px) {#sp-top1 .sppb-container-inner {max-width: 960px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 1200px) {#sp-top1  .sppb-container-inner {max-width: 1140px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}


@media (min-width: 320px) {.contained-row  .sppb-container-inner {max-width: 400px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 576px) {.contained-row  .sppb-container-inner {max-width: 540px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 768px) {.contained-row  .sppb-container-inner {max-width: 720px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 992px) {.contained-row .sppb-container-inner {max-width: 960px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}
@media (min-width: 1200px) {.contained-row  .sppb-container-inner {max-width: 1140px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}




/* J4 Optimization */

.sp-page-title .breadcrumb .float-start {display: none !important;}
.sp-megamenu-wrapper, #sp-header > .container > .container-inner > .row > div > .sp-column,
#sp-header > .row > div > .sp-column  {display: block !important;}

#sp-header .sp-module {
	margin-left: 0px !important; 
}

@media (min-width: 1400px) {#sp-footer  .sppb-container-inner {max-width: 1320px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}

@media (min-width: 1400px) {#sp-top1  .sppb-container-inner {max-width: 1320px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}

@media (min-width: 1400px) {.sppb-section  .sppb-row-container {max-width: 1320px;width: 100%;
padding-right: 15px;
padding-left: 15px;
margin-right: auto;
margin-left: auto;}}";s:6:"output";s:0:"";}