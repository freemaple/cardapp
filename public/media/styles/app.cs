@charset "UTF-8";
/*公共基础*/
/*公共基础结束*/
/*公共基础*/
html {
  max-width: 750px;
  font-size: calc(100vw / 7.5);
  background-color: #f1f1f1; }

@media screen and (min-width: 415px) {
  html {
    font-size: 55px; } }
/*公共样式*/
body, html {
  width: 100%;
  margin: 0px auto;
  margin-bottom: 0px !important; }

@media (min-width: 769px) {
  body, html {
    max-width: 640px; } }
body {
  font-family: "San Francisco", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 14px;
  color: #444444;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  width: 100%; }

.overfixed, body.overfixed {
  overflow: hidden; }

* {
  border: 0;
  outline: 0;
  -webkit-text-size-adjust: none;
  -webkit-tap-highlight-color: transparent;
  margin: 0px;
  padding: 0px;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  word-wrap: break-word;
  word-break: break-word; }

ul {
  list-style: none; }

ul li {
  list-style: none; }

a {
  text-decoration: none;
  cursor: pointer;
  color: #333; }

.clearfix:after {
  clear: both;
  content: ' ';
  display: block;
  visibility: none;
  height: 1%; }

input[type=button], input[type=submit], input[type=reset] {
  -webkit-appearance: none;
  cursor: pointer; }

input[type=checkbox], input[type=radio] {
  margin-right: 5px;
  vertical-align: middle; }

.pull-right {
  float: right; }

.u-link {
  text-decoration: underline; }

.a-link:hover {
  text-decoration: underline; }

.img[src=""], img:not([src]) {
  opacity: 0; }

.phpdebugbar {
  display: none; }

.button-block.disabled {
  background-color: #adadac;
  color: #333333;
  cursor: not-allowed; }

.distpicker select {
  float: left;
  width: 32%;
  margin-right: 1.33%; }

.bg-f {
  background-color: #fff; }

.pd-10 {
  padding: 10px; }

.search-select {
  height: 30px;
  padding: 0px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #444444;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px; }

.search-input {
  height: 30px;
  padding: 0px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #444444;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px; }

@media (max-width: 414px) {
  .wangEditor-drop-panel {
    width: 100% !important;
    left: 0px !important;
    margin-left: 0px !important; } }
.swiper-container {
  width: 100%; }

.swiper-wrapper img {
  width: 100%; }

.swiper-slide {
  text-align: center;
  font-size: 18px;
  background: #fff;
  /* Center slide text vertically */
  display: -webkit-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  -webkit-justify-content: center;
  justify-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  -webkit-align-items: center;
  align-items: center; }

/*公共样式*/
/*动画*/
.slideLeft {
  position: relative;
  -webkit-animation: slideLeft .6s ease;
  -moz-animation: slideLeft .6s ease;
  -ms-animation: slideLeft .6s ease;
  -o-animation: slideLeft .6s ease; }

.slideRight {
  position: relative;
  -webkit-animation: slideRight .6s ease;
  -moz-animation: slideRight .6s ease;
  -ms-animation: slideRight .6s ease;
  -o-animation: slideRight .6s ease; }

@-webkit-keyframes slideLeft {
  0% {
    opacity: 0;
    left: 100%; }
  100% {
    opacity: 1;
    left: 0; } }
@keyframes slideLeft {
  0% {
    opacity: 0;
    left: 100%; }
  100% {
    opacity: 1;
    left: 0; } }
@-moz-keyframes slideLeft {
  0% {
    opacity: 0;
    left: 100%; }
  100% {
    opacity: 1;
    left: 0; } }
@-o-keyframes slideLeft {
  0% {
    opacity: 0;
    left: 100%; }
  100% {
    opacity: 1;
    left: 0; } }
@-ms-keyframes slideLeft {
  0% {
    opacity: 0;
    left: 30px; }
  100% {
    opacity: 1;
    left: 0; } }
@-webkit-keyframes slideRight {
  0% {
    opacity: 0;
    left: -100%; }
  100% {
    opacity: 1;
    left: 0; } }
@keyframes slideRight {
  0% {
    opacity: 0;
    left: -100%; }
  100% {
    opacity: 1;
    left: 0; } }
@-moz-keyframes slideRight {
  0% {
    opacity: 0;
    left: -100%; }
  100% {
    opacity: 1;
    left: 0; } }
@-o-keyframes slideRight {
  0% {
    opacity: 0;
    left: -100%; }
  100% {
    opacity: 1;
    left: 0; } }
@-ms-keyframes slideRight {
  0% {
    opacity: 0;
    left: -100%; }
  100% {
    opacity: 1;
    left: 0; } }
@keyframes rotate {
  0% {
    transform: rotate(0); }
  50% {
    transform: rotate(180deg); }
  100% {
    transform: rotate(360deg); } }
.rotate {
  transition: 0.5s;
  transform-origin: 30px 30px;
  animation: rotate 2s linear infinite;
  /*开始动画后无限循环，用来控制rotate*/
  -webkit-transition: 0.5s;
  -webkit-transform-origin: 30px 30px;
  -webkit-animation: rotate 4s linear infinite;
  /*开始动画后无限循环，用来控制rotate*/ }

.rotate:hover {
  -webkit-animation: unset;
  animation: unset; }

@keyframes lds-rolling {
  0% {
    -webkit-transform: translate(-50%, -50%) rotate(0deg);
    transform: translate(-50%, -50%) rotate(0deg); }
  100% {
    -webkit-transform: translate(-50%, -50%) rotate(360deg);
    transform: translate(-50%, -50%) rotate(360deg); } }
@-webkit-keyframes lds-rolling {
  0% {
    -webkit-transform: translate(-50%, -50%) rotate(0deg);
    transform: translate(-50%, -50%) rotate(0deg); }
  100% {
    -webkit-transform: translate(-50%, -50%) rotate(360deg);
    transform: translate(-50%, -50%) rotate(360deg); } }
.lds-css {
  width: 200px;
  margin: auto; }

.lds-rolling {
  width: 100%;
  height: 100%;
  position: relative; }

.lds-rolling div,
.lds-rolling div:after {
  position: absolute;
  width: 80px;
  height: 80px;
  border: 15px solid #fe5430;
  border-top-color: transparent;
  border-radius: 50%; }

.lds-rolling div {
  -webkit-animation: lds-rolling 1s linear infinite;
  animation: lds-rolling 1s linear infinite;
  top: 50px;
  left: 50px; }

.lds-rolling div:after {
  -webkit-transform: rotate(90deg);
  transform: rotate(90deg); }

.lds-rolling {
  width: 200px;
  height: 200px;
  -webkit-transform: translate(-50px, -50px) scale(1) translate(50px, 50px);
  transform: translate(-50px, -50px) scale(1) translate(50px, 50px); }

/*动画结束*/
/*空数据块*/
.no-results {
  padding: 40px 0px 40px 0px;
  text-align: center; }

.no-results .result-content {
  padding: 30px 0px; }

.no-results p {
  line-height: 25px;
  font-size: 15px; }

.no-results .oops {
  font-size: 36px;
  font-weight: 500;
  padding-bottom: 20px; }

.no-result-image {
  background-image: url(../images/empty.png?1);
  display: block;
  width: 180px;
  height: 160px;
  background-repeat: no-repeat;
  margin: auto;
  background-size: 100%; }

.no-results .result-img {
  margin-top: 60px; }

.no-results .result-content {
  margin-top: 120px; }

/*空数据块结束*/
/*上传块*/
.upload-form {
  position: relative; }

.upload-form .upload-file {
  position: absolute;
  left: 0px;
  top: 0px;
  display: block;
  z-index: 10000;
  width: 100%;
  height: 100%;
  opacity: 0;
  -webkit-opacity: 0;
  -moz-opacity: 0;
  filter: alpha(opacity=0);
  -khtml-opacity: 0;
  cursor: pointer; }

/*上传块*/
/*头像*/
.avatar-img-default {
  display: block;
  width: 40px;
  height: 40px;
  margin: 0px auto;
  background-size: 40px auto;
  background-image: url(../images/avatar.png?1);
  background-repeat: no-repeat;
  background-position: center center; }

/*头像*/
/*help*/
.help-box {
  margin: 20px 0px;
  padding: 20px 10px;
  background-color: #ffffff; }

.help-box * {
  max-width: 100%; }

.help-box h1 {
  margin-bottom: 10px; }

.help-box p {
  line-height: 25px;
  font-size: 14px;
  text-indent: 1em; }

/*help*/
.mobile-header-box {
  position: relative; }

.mobile-header-box .menu {
  position: absolute;
  top: 0;
  left: 10px;
  height: 44px;
  line-height: 44px;
  color: #5f5f5f; }

.mobile-header-box .menu i {
  font-size: 24px; }

.top-seach {
  height: 30px;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  font-size: 1.4rem;
  padding: 0 5px;
  margin: 7px 10px 7px 50px;
  background-color: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 5px;
  position: relative;
  padding-left: 30px; }

.top-seach input {
  width: 100%; }

.top-seach i {
  position: absolute;
  font-size: 20px;
  top: 0px;
  left: 5px;
  color: #777;
  margin-top: 4px; }

/*列表*/
.list-item {
  margin-top: 5px;
  width: 100%;
  padding: 10px;
  margin-bottom: 5px;
  position: relative;
  background: #ffffff; }

.list-item-header {
  padding: 0px 0px 5px 0px;
  border-bottom: 1px solid #e2e2e2; }

.list-item-header .list-no {
  font-size: 12px;
  color: #444444; }

.list-item-header .list-status-text {
  font-size: 12px;
  float: right;
  color: #f00; }

.list-item-header .list-time-text {
  font-size: 12px;
  float: right;
  color: #999; }

.list-item .img {
  float: left;
  width: 120px;
  min-height: 120px; }

.list-item .img img {
  width: 120px; }

.list-item .info {
  width: 100%; }

.list-item .info-box {
  padding-left: 140px;
  padding-right: 20px; }

.list-item .name {
  color: #444444;
  font-size: 12px;
  line-height: 18px; }

@media (max-width: 360px) {
  .list-item .img {
    width: 90px; }

  .list-item .img img {
    width: 90px; }

  .list-item .info-box {
    padding-left: 50px; } }
.list-item .checkbox {
  display: block;
  width: 24px;
  height: 24px;
  background-color: #e2e2e2;
  border-radius: 50%;
  text-align: center;
  font-size: 14px;
  color: #fff;
  padding-top: 3px;
  color: #ffffff; }

.list-item.selected .checkbox {
  background-color: #FF9800; }

/*list-item结束*/
.pup-a-tel {
  display: inline-block;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 1px solid #1afa29;
  line-height: 34px;
  text-align: center;
  color: #1afa29; }

.pup-a-tel .iconfont {
  color: #1afa29; }

/*消息提示框*/
.msg_alert {
  position: relative;
  display: none; }

.msg_alert_content {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  text-align: center; }

.site_msg_art .msg_alert_content {
  margin: 0px;
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px; }

.msg_alert_close {
  position: absolute;
  top: 50%;
  margin-top: -20px;
  right: 10px;
  color: #fff;
  font-size: 30px;
  cursor: pointer; }

.msg_alert_close:hover {
  color: #333; }

.msg_alert.success .msg_alert_content {
  color: #31708f;
  background-color: #d9edf7;
  border-color: #bce8f1;
  font-size: 15px; }

.msg_alert.error .msg_alert_content {
  color: #fff;
  background-color: #f00;
  border-color: #ebccd1;
  line-height: 20px;
  font-size: 14px; }

.msg_alert.show {
  display: block; }

/*提示框结束*/
.video-js .vjs-big-play-button {
  top: 50% !important;
  left: 50% !important;
  transform: translate(-50%, -50%) !important;
  -webkit-transform: translate(-50%, -50%) !important; }

.position-select {
  display: inline-block;
  margin-right: -4px;
  width: 20%;
  padding-right: 3px; }

.position-select .form-control {
  padding: 0px; }

/*分页栏*/
.pager_block {
  padding: 10px 20px 10px 20px; }

.pager_block .item_status {
  margin-top: 15px;
  color: #777777; }

.pager_block .total_item_info {
  margin-right: 10px; }

.pager_block .total_item {
  font-size: 14px;
  color: #000; }

.pager_block .current_page_item {
  color: #000; }

.pagination {
  display: inline-block; }

.pagination > li {
  display: inline; }

.pagination > li > a, .pagination > li > span {
  position: relative;
  float: left;
  padding: 6px 12px;
  margin-left: 5px;
  line-height: 1.42857143;
  color: #337ab7;
  text-decoration: none;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px; }

.pagination > li > a:hover, .pagination > li > span:hover {
  z-index: 3;
  color: #23527c;
  background-color: #eee;
  border-color: #ddd; }

.pagination > .active > a, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:hover {
  z-index: 2;
  color: #fff;
  cursor: default;
  background-color: #337ab7;
  border-color: #337ab7; }

.pagination > .disabled > a, .pagination > .disabled > a:hover, .pagination > .disabled > span, .pagination > .disabled > span:hover {
  color: #777;
  cursor: not-allowed;
  background-color: #fff;
  border-color: #ddd; }

.pagination-lg > li > a, .pagination-lg > li > span {
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.3333333; }

.pagination-sm > li > a, .pagination-sm > li > span {
  padding: 5px 10px;
  font-size: 12px;
  line-height: 1.5; }

.pager {
  margin: 0px 0 20px 0;
  text-align: right;
  list-style: none; }

.pager_align {
  text-align: center; }

/*分页栏结束*/
.rote_wrap {
  height: 100%;
  position: relative;
  -webkit-transform-style: preserve-3d;
  -webkit-perspective: 0px;
  -moz-transform-style: preserve-3d;
  -moz-perspective: 0px;
  -webkit-animation: mydhua 5s ease infinite;
  -moz-animation: mydhua 5s ease infinite; }

.rote_box {
  width: 100px;
  height: 100px;
  position: absolute;
  top: 50%;
  left: 50%;
  margin: -50px 0 0 -50px;
  line-height: 200px;
  text-align: center;
  font-size: 48px;
  color: white; }

.rote_wrap .box1 {
  -webkit-transform: rotatey(90deg) translatez(-50px);
  -moz-transform: rotatey(90deg) translatez(-50px);
  background: rgba(128, 0, 128, 0.5); }

.rote_wrap .box2 {
  -webkit-transform: rotatey(90deg) translatez(50px);
  -moz-transform: rotatey(90deg) translatez(50px);
  background: rgba(255, 0, 255, 0.5); }

.rote_wrap .box3 {
  -webkit-transform: rotatex(90deg) translatez(50px);
  -moz-transform: rotatex(90deg) translatez(50px);
  background: rgba(255, 153, 204, 0.5); }

.rote_wrap .box4 {
  -webkit-transform: rotatex(90deg) translatez(-50px);
  -moz-transform: rotatex(90deg) translatez(-50px);
  background: rgba(0, 204, 255, 0.5); }

.rote_wrap .box5 {
  -webkit-transform: translatez(-50px);
  -moz-transform: translatez(-50px);
  background: rgba(153, 204, 255, 0.5); }

.rote_wrap .box6 {
  -webkit-transform: translatez(50px);
  -moz-transform: translatez(50px);
  background: rgba(0, 255, 255, 0.5); }

@-webkit-keyframes mydhua {
  0% {
    -webkit-transform: rotateX(0deg) rotateY(0deg);
    -webkit-transform-origin: center center; }
  100% {
    -webkit-transform: rotateX(30deg) rotateY(360deg);
    -webkit-transform-origin: center center; } }
@-moz-keyframes mydhua {
  0% {
    -moz-transform: rotateX(0deg) rotateY(0deg);
    -webkit-transform-origin: center center; }
  100% {
    -moz-transform: rotateX(30deg) rotateY(360deg);
    -webkit-transform-origin: center center; } }
/*公共基础结束*/
/*组件*/
/*轮播*/
.slider {
  overflow-x: hidden;
  position: relative;
  height: auto;
  min-height: 80px; }

.slider-img-wrap {
  overflow: hidden; }

.slider-item {
  float: left; }

.slider-img-wrap img {
  width: 100%; }

.slider-num-wrap {
  position: absolute;
  bottom: 20px;
  text-align: center; }

.slider-num-wrap li {
  display: inline-block;
  margin-left: 4px;
  width: 12px;
  height: 12px;
  border-radius: 6px;
  text-indent: -9999px;
  background-color: #ffffff; }

.slider-num-wrap li.js-slider-num-cur {
  background-color: #222222; }

/*轮播结束*/
/*返回顶部*/
.btn-scroll-top {
  position: fixed;
  right: 10px;
  bottom: 150px;
  display: none;
  background-color: #f1f1f1;
  background-repeat: no-repeat;
  background-position: center center;
  border-radius: 50%;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  height: 32px;
  width: 32px;
  line-height: 32px; }

.btn-scroll-to-top {
  cursor: pointer;
  display: block;
  color: #222;
  text-align: center;
  font-size: 18px;
  vertical-align: middle; }

/*返回顶部结束*/
/*弹出层开始*/
.overlay {
  margin: 0px;
  padding: 0px;
  border: none;
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0px;
  left: 0px;
  display: none;
  background-color: #cccccc;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 9999;
  opacity: 1;
  -webkit-opacity: 1;
  -moz-opacity: 1;
  filter: alpha(opacity=80); }

.layerbox {
  position: fixed;
  top: 0px;
  left: 0;
  width: 100%;
  height: 100%;
  overflow-y: auto;
  z-index: 1000;
  visibility: hidden; }

.layer-top {
  position: absolute;
  overflow-y: auto; }

@media (min-width: 769px) {
  .layerbox {
    max-width: 640px;
    left: 50%;
    transform: translateX(-50%);
    -webkit-transform: translateX(-50%); } }
.layerbox.show {
  visibility: visible; }

.layerbox-wrapper {
  color: #444444;
  border: 3px solid transparent;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
  z-index: 50;
  position: absolute;
  width: auto;
  max-width: 100%; }

.layerbox-wrapper .layerbox-content {
  border-radius: 4px;
  background-color: #fff; }

.layerbox-wrapper-title {
  height: 40px;
  line-height: 40px;
  overflow: hidden;
  color: #666;
  padding: 0 10px;
  font-size: 14px;
  border-radius: 4px 4px 0 0;
  background-color: #f9f9f9; }

.layerbox-close-btn {
  position: absolute;
  font-family: arial;
  font-size: 30px;
  font-weight: 700;
  color: #999999;
  text-decoration: none;
  right: 15px;
  z-index: 10000; }

.layer-center .layerbox-close-btn {
  color: #222;
  right: -7px;
  top: -12px;
  background-color: #e0e0e0;
  border-radius: 50%;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  width: 25px;
  height: 25px;
  text-align: center;
  line-height: 25px; }

.layerbox-close-btn:hover {
  color: #444444; }

.layerbox-wrapper-text {
  line-height: 22px; }

.layerbox-footer {
  text-align: center;
  border-top: 1px solid #eee;
  border-radius: 0 0 13px 13px; }

.layerbox-footer:after {
  content: '';
  display: block;
  height: 0;
  overflow: hidden;
  visibility: hidden;
  clear: both; }

.layerbox-button {
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  cursor: pointer;
  display: block;
  height: 34px;
  line-height: 34px;
  color: #007aff;
  overflow: hidden;
  font-size: 17px;
  color: #007aff;
  text-align: center;
  text-overflow: ellipsis;
  white-space: nowrap;
  -webkit-box-flex: 1; }

.layer-confirm .layerbox-button {
  width: 50%;
  display: inline-block;
  margin-right: -5px;
  border-right: 1px solid #e2e2e2; }

.layer-message {
  padding: 20px;
  min-width: 180px;
  text-align: center;
  border-radius: 13px; }

.layer-message-content {
  display: inline-block;
  font-size: 14px;
  vertical-align: middle; }

.show-layer-message .layerbox-content {
  min-width: 250px;
  max-width: 750px; }

.popover-inner {
  position: fixed;
  z-index: 10001;
  left: 50%;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%); }

.popover-inner.bottom {
  bottom: 100px; }

.popover-inner.center {
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%); }

.popover-inner-content {
  padding: 10px;
  width: auto;
  background-color: #f00;
  color: #fff;
  z-index: 10001;
  line-height: 17px;
  min-width: 120px;
  text-align: center;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  max-width: 750px;
  text-align: center;
  margin: 0px auto;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); }

.popover-inner.success .popover-inner-content {
  background-color: #4caf50; }

.layer-cover {
  margin: 0px;
  padding: 0px;
  border: none;
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0px;
  left: 0px;
  display: none;
  background-color: #ccc;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 99999;
  opacity: 0.8;
  -webkit-opacity: 0.8;
  -moz-opacity: 0.8;
  filter: alpha(opacity=80); }

.layer-load {
  position: fixed;
  z-index: 100000;
  top: 50%;
  left: 50%;
  width: 120px;
  height: 120px;
  margin-top: -60px;
  margin-left: -60px;
  border: 3px solid transparent;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  display: none; }

.layer-loading {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  margin: 0 auto;
  margin-top: 20px;
  position: relative;
  border: 5px solid #666666;
  animation: turn 2s linear infinite;
  -webkit-animation: turn 2s linear infinite;
  -moz-animation: turn 2s linear infinite; }

.layer-loading span {
  display: inline-block;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: #666666;
  position: absolute;
  left: 50%;
  margin-top: -15px;
  margin-left: -15px; }

@keyframes turn {
  0% {
    transform: rotate(0deg); }
  100% {
    transform: rotate(360deg); } }
@-webkit-keyframes turn {
  0% {
    -webkit-transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(360deg); } }
@-moz-keyframes turn {
  0% {
    -moz-transform: rotate(0deg); }
  100% {
    -moz-transform: rotate(360deg); } }
.layerbox-wrapper {
  -webkit-transition-duration: 0.23s;
  transition-duration: 0.23s;
  max-width: 100%; }

.show-layer-message .layerbox-wrapper {
  opacity: 0.5;
  -webkit-transition-property: -webkit-transform,opacity;
  transition-property: transform,opacity; }

.show-layer-message.show .layerbox-wrapper {
  opacity: 1; }

.layer-top .layerbox-wrapper .layerbox-content, .layer-bottom .layerbox-wrapper .layerbox-content {
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px; }

.layer-top .layerbox-wrapper {
  width: 100%;
  height: 100%;
  left: 0px;
  top: 0px;
  box-shadow: none;
  border: none;
  transition: transform ease-in-out 0.5s;
  -webkit-transition: transform ease-in-out 0.5s;
  -moz-transition: transform ease-in-out 0.5s; }

.layer-top .layerbox-wrapper .layerbox-content {
  min-height: 100%; }

.layer-bottom .layerbox-wrapper {
  left: 0;
  bottom: 0px;
  box-shadow: none;
  border: none;
  width: 100%;
  transition: transform ease-in-out 0.5s;
  -webkit-transition: transform ease-in-out 0.5s;
  -moz-transition: transform ease-in-out 0.5s; }

/*弹出层结束*/
/*块加载层*/
.waiting-load-block {
  padding: 20px 0px;
  text-align: center; }

.waiting-loading {
  display: block;
  width: 64px;
  height: 64px;
  margin: 0px auto;
  background-image: url(../images/loader.gif?1);
  background-repeat: no-repeat;
  background-position: center center; }

/*块加载层结束*/
/*panel开始*/
.block-panel-title {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 15px; }

.panel-title {
  background-color: #ffffff;
  padding: 10px; }

/*panel结束*/
/*页签*/
.tab-menu {
  margin: 10px 0px; }

.tab-menu .tab-item a {
  display: block; }

.tab-menu .tab-item.current a {
  color: #FF9800; }

.box-tab .tab-content > div {
  display: none; }

.box-tab .tab-content > div.current {
  display: block; }

/*页签结束*/
/*组件结束*/
/*字体设置*/
@font-face {
  font-family: 'San Francisco';
  src: url("../fonts/sanfranciscodisplay-regular.eot");
  src: url("../fonts/sanfranciscodisplay-regular.eot?#iefix") format("embedded-opentype"), url("../fonts/sanfranciscodisplay-regular.woff2?1") format("woff2"), url("../fonts/sanfranciscodisplay-regular.woff?1") format("woff"), url("../fonts/sanfranciscodisplay-regular.ttf") format("truetype"), url("../fonts/sanfranciscodisplay-regular.svg#ralewayregular") format("svg");
  font-weight: normal;
  font-style: normal; }
/*公共*/
@media (min-width: 1024px) {
  ::-webkit-scrollbar {
    width: 10px; }

  ::-webkit-scrollbar-track {
    background-color: #eaeaea;
    border-left: 1px solid #ccc; }

  ::-webkit-scrollbar-thumb {
    background-color: #aaaaaa; }

  ::-webkit-scrollbar-thumb:hover {
    background-color: #aaaaaa; }

  ::-webkit-scrollbar-thumb:active {
    background-color: #aaaaaa; }

  ::-webkit-scrollbar:horizontal {
    height: 5px; } }
.lazy {
  min-height: 50px;
  background-image: url(../images/loading.gif?1);
  background-repeat: no-repeat;
  background-position: center center; }

.errormsg {
  color: #e74c3c; }

/*公共*/
/*文本*/
.a-link {
  text-decoration: underline;
  color: #de5430; }

.text-left {
  text-align: left; }

.text-center {
  text-align: center; }

.text-right {
  text-align: right; }

.text-blue {
  color: #00f; }

.text-red {
  color: #e74c3c; }

.text-primary {
  color: #FF9800; }

.text-success {
  color: #5eb95e; }

.text-info {
  color: #5bc0de; }

.text-warning {
  color: #f37b1d; }

.text-danger {
  color: #dd514c; }

/*文本*/
/*按钮*/
.btn {
  display: inline-block;
  font-size: 14px;
  padding: 0px 15px;
  margin-bottom: 0;
  font-weight: normal;
  height: 40px;
  line-height: 40px;
  max-height: 50px;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
  touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  border: 1px solid transparent;
  border-radius: 8px;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  font-size: 14px; }

.btn-small {
  height: 34px;
  line-height: 34px; }

.btn:hover {
  -webkit-transition: background-color, color, border-color ease .23s, ease .23s, ease .23s;
  -moz-transition: background-color, color, border-color ease .23s, ease .23s, ease .23s;
  -o-transition: background-color, color, border-color ease .23s, ease .23s, ease .23s;
  transition: background-color, color, border-color ease .23s, ease .23s, ease .23s; }

.btn-default {
  color: #333;
  border: 1px solid #e2e2e2;
  background-color: #ffffff; }

.btn-primary {
  color: #fff;
  background-color: #FF9800; }

.btn-primary:hover, .btn-primary:focus, .btn-primary.focus, .btn-primary.active {
  color: #fff;
  background-color: #ffc107; }

.btn-info {
  color: #fff;
  background-color: #5bc0de; }

.btn-info:hover, .btn-info:focus, .btn-info:active, .btn-info.active {
  color: #fff;
  background-color: #269abc; }

.btn-success {
  color: #fff;
  background-color: #5eb95e; }

.btn-success:hover, .btn-success:focus, .btn-success.focus, .btn-success.active {
  color: #fff;
  background-color: #429842; }

.btn-warning {
  color: #fff;
  background-color: #f37b1d; }

.btn-warning:hover, .btn-warning:focus, .btn-warning.focus, .btn-warning.active {
  color: #fff;
  background-color: #fff; }

.btn-danger {
  color: #fff;
  background-color: #dd514c; }

.btn-danger:hover, .btn-danger:focus, .btn-danger:active {
  color: #fff;
  background-color: #c62b26; }

.btn.disabled {
  color: #fff !important;
  background-color: #dddddd !important;
  border-color: #dddddd !important;
  cursor: not-allowed !important; }

.btn-block {
  width: 100%; }

.operate-btn {
  background-color: #ffffff;
  border: 1px solid #e2e2e2;
  padding: 5px;
  margin-right: 10px;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  color: #f00;
  border: 1px solid #FF9800; }

.operate-btn:hover {
  background-color: #fe5430;
  color: #fff; }

/*按钮结束*/
/*表单*/
.form-group {
  margin-bottom: 15px;
  position: relative; }

.form-group-label {
  color: #999999;
  display: block;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 5px; }

.form-control {
  display: block;
  width: 100%;
  height: 40px;
  padding: 0px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #444444;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px; }

.form-control.disabled {
  background-color: #eeeeee; }

textarea.form-control {
  height: auto;
  padding: 10px 12px; }

.form-group-list .form-group {
  float: left;
  width: 50%; }

.form-group-list .form-group:first-child {
  padding-right: 10px; }

.form-line {
  float: left;
  margin-right: 20px; }

.form-line-label {
  display: inline-block;
  vertical-align: middle;
  width: auto;
  margin-right: 10px; }

.form-line-input {
  display: inline-block;
  vertical-align: middle;
  width: auto; }

.form-icon-text {
  position: relative; }

.form-icon-text .iconfont {
  position: absolute;
  line-height: 40px;
  padding: 0px 10px;
  font-size: 24px;
  color: #222; }

.form-icon-text .form-control {
  border: 1px solid #e2e2e2;
  padding-left: 40px;
  width: 100%; }

/*表单结束*/
/*主题结束*/
/*页面布局*/
/*layout*/
/*移动端头部*/
.mobile-header-box {
  height: 44px;
  background-color: #ff9800;
  border-bottom: 1px solid #e2e2e2;
  text-shadow: none;
  position: fixed;
  display: block;
  width: 100%;
  z-index: 4;
  left: 0px;
  top: 0px;
  font-size: 12px;
  color: #fff; }

.mobile-header-box a {
  color: #fff; }

.mobile-header:after {
  clear: both;
  content: ' ';
  display: block;
  height: 44px; }

@media (min-width: 769px) {
  .mobile-header-box {
    max-width: 640px;
    left: 50%;
    transform: translateX(-50%);
    -webkit-transform: translateX(-50%); } }
.mobile-header-title {
  display: block;
  width: 100%;
  text-align: center;
  line-height: 44px;
  font-size: 16px;
  text-align: center;
  white-space: nowrap;
  z-index: 1; }

.mobile-header-back {
  position: absolute;
  left: 0px;
  font-size: 30px;
  height: 44px;
  line-height: 44px; }

.mobile-header-back a {
  display: block;
  padding: 0px 5px; }

.mobile-header-back .icon-back {
  font-size: 24px; }

.mobile-header-right {
  position: absolute;
  right: 20px;
  top: 0px;
  line-height: 44px;
  height: 44px; }

.mobile-header-right .share-icon {
  display: inline-block;
  vertical-align: middle;
  height: 44px; }

.mobile-header-right .share-icon .icon-share1 {
  font-size: 20px; }

.share-header-right {
  line-height: unset; }

.wrap-content {
  padding-bottom: 60px; }

/*头部结束*/
/*底部*/
.mobile-footer {
  position: fixed;
  left: 0;
  width: 100%;
  bottom: 0;
  border-top: 1px solid #e2e2e2;
  height: 49px;
  background-color: #ffffff;
  text-align: center;
  z-index: 100;
  padding: 0px;
  display: block;
  width: 100%; }

.mobile-footer .current {
  transition: 0.23s ease-out;
  -webkit-transition: 0.23s ease-out;
  -moz-transition: 0.23s ease-out; }

.mobile-footer .foot-nav-info li {
  float: left;
  height: 49px;
  width: 20%;
  display: table; }

.site_footer .foot-nav-info li {
  width: 25%; }

.mobile-footer .foot-nav-info li a {
  color: #00f;
  display: block;
  text-align: center;
  line-height: 14px;
  display: table-cell;
  vertical-align: middle; }

.mobile-footer .current {
  transition: 0.23s ease-out;
  -webkit-transition: 0.23s ease-out;
  -moz-transition: 0.23s ease-out; }

.mobile-footer .iconfont {
  font-size: 20px;
  color: #666666; }

.mobile-footer .text {
  font-size: 12px;
  color: #999; }

.foot-nav-info li.current .iconfont, .foot-nav-info li.current .text {
  color: #FF9800; }

@media (min-width: 769px) {
  .mobile-footer {
    max-width: 640px;
    left: 50%;
    transform: translateX(-50%);
    -webkit-transform: translateX(-50%); } }
.mobile-footer-operate {
  padding: 0px 10px; }

.mobile-footer .operate-box {
  height: 100%;
  text-align: right; }

.mobile-footer .operate-box li {
  display: inline-block;
  vertical-align: middle;
  height: 49px;
  line-height: 49px; }

.mobile-footer .operate-box li a {
  height: 34px;
  line-height: 34px; }

.u-message_number {
  position: absolute;
  border-radius: 50%;
  height: 16px;
  width: 16px;
  right: -14px;
  top: -30px;
  background-color: #f00;
  color: #fff; }

/*底部结束*/
/*layout结束*/
.j_detail {
  max-width: 100%;
  overflow-x: hidden; }

.toptit {
  position: relative;
  width: 100%;
  padding: 20px 30px 20px 10px;
  min-height: 100px;
  text-align: center;
  background-color: #fff;
  color: #444444; }

.toptit .musicBtn {
  right: 4px; }

.toptit h3 {
  line-height: 40px;
  font-size: 26px;
  color: #444444;
  font-family: Microsoft YaHei; }

.post-user-info {
  padding: 10px;
  background: #fff;
  position: relative; }

.post-user-info .avatar-info {
  width: 40px;
  height: 40px;
  z-index: 2; }

.post-user-info .avatar-info img {
  width: 40px;
  height: 40px; }

.post-user-info .title {
  position: absolute;
  width: 100%;
  text-align: center;
  top: 50%;
  margin-top: -10px;
  font-size: 16px;
  color: #444444;
  border-bottom: 1px solid #e2e2e2;
  padding-bottom: 20px; }

.post-user-info .rightbox {
  position: absolute;
  right: 10px;
  top: 0px; }

.post-user-info .rightbox a {
  display: block;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  background: #ff9800;
  color: #fff;
  text-align: center;
  vertical-align: middle;
  margin-top: 10px; }

.post-user-info .rightbox a span {
  margin-top: 7px;
  display: inline-block;
  vertical-align: middle; }

.little-info {
  padding: 15px 15px;
  font-size: 13px;
  color: #999;
  background: #fff; }

.little-info span {
  margin-right: 15px; }

.little-info span em {
  margin-left: 5px; }

.description-block * {
  max-width: 100%;
  line-height: 25px; }

.box-description {
  line-height: 20px;
  background: #fff;
  padding: 20px 10px; }

.box-description * {
  max-width: 100%; }

.post-footer li {
  width: 50%;
  float: left; }

.post-footer li a {
  display: block;
  width: 100%;
  height: 100%;
  line-height: 49px;
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px; }

@keyframes r {
  0% {
    width: 50px;
    border-radius: 50%; }
  100% {
    width: 100%;
    border-radius: 0%; } }
.b_img {
  transform: scale(0, 0);
  -ms-transform: scale(0, 0);
  /* IE 9 */
  -moz-transform: scale(0, 0);
  /* Firefox */
  -webkit-transform: scale(0, 0);
  /* Safari and Chrome */
  -o-transform: scale(0, 0);
  /* Opera */
  transition: 2s;
  -webkit-transition: 2s; }

.b_img.x {
  transform: scale(1, 1);
  -ms-transform: scale(1, 1);
  /* IE 9 */
  -moz-transform: scale(1, 1);
  /* Firefox */
  -webkit-transform: scale(1, 1);
  /* Safari and Chrome */
  -o-transform: scale(1, 1);
  /* Opera */ }

.rimg {
  transition: 0.5s;
  transform-origin: 30px 30px;
  animation: r 2s linear infinite;
  /*开始动画后无限循环，用来控制rotate*/
  -webkit-transition: 0.5s;
  -webkit-transform-origin: 30px 30px;
  -webkit-animation: r 4s linear infinite;
  /*开始动画后无限循环，用来控制rotate*/ }

.uimg {
  transition: 0.5s;
  transform-origin: 30px 30px;
  animation: u 2s linear infinite;
  /*开始动画后无限循环，用来控制rotate*/
  -webkit-transition: 0.5s;
  -webkit-transform-origin: 30px 30px;
  -webkit-animation: u 4s linear infinite;
  /*开始动画后无限循环，用来控制rotate*/ }

.social-share a {
  margin: 5px; }

.sysDefault-ad1 {
  padding: 40px 0px 20px 0px; }

.sysDefault-ad1-info {
  border: 1px solid #d7d7d7;
  padding-bottom: 20px;
  position: relative;
  padding-top: 50px;
  text-align: center;
  font-size: 14px;
  color: #424242;
  background-color: #f9f9f9;
  line-height: 25px; }

.faceImgbox {
  position: absolute;
  top: -30px;
  left: 50%;
  margin-left: -50px;
  width: 100px;
  text-align: center; }

.faceImgbox img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  border: 1px solid #f2f2f2; }

.sysDefault-ad1-info .name {
  font-size: 16px; }

.sysDefault-ad1-info .tellbox {
  color: #1f8ff3; }

.mt-10 {
  margin-top: 10px; }

.codeShowbox.custormCode {
  margin: 10px 20px 0 20px;
  border-bottom: 1px dashed #d7d7d7; }

.sysDefault-ad1-info .codeImgbox {
  width: 120px;
  max-height: 200px;
  overflow: hidden;
  margin: 20px auto 0;
  border: 1px solid #f5f5f5;
  padding: 9px; }

.sysDefault-ad1-info .codeImgbox img {
  width: 100%; }

.s-img-box .ad_link {
  position: absolute;
  bottom: 10px;
  right: 5px;
  background-color: #ff9800;
  color: #fff;
  padding: 0px 5px;
  height: 20px;
  line-height: 20px;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  opacity: 0.8; }

/*产品详情*/
.goods-header-info {
  padding: 5px 8px  0px 8px;
  background-color: #fff;
  position: fixed;
  min-height: 90px;
  width: 100%;
  max-width: 640px;
  z-index: 10; }

.goods-header-info .image {
  z-index: 2;
  position: absolute; }

.goods-header-info .info-box {
  float: left;
  width: 100%;
  padding-left: 90px;
  width: 100%; }

.goods-header-info .goods-name {
  font-size: 13px;
  font-weight: 500;
  color: #444444;
  max-height: 54px;
  overflow: hidden; }

.goods-header-info .price-info {
  padding: 0px 0px; }

.goods-header-info .price {
  color: #fe5430;
  font-size: 18px;
  margin-right: 10px; }

.goods-header-info .s_price {
  text-decoration: line-through;
  color: #999;
  font-size: 12px; }

.goods-detail-block {
  background-color: #fff;
  padding: 10px 0px; }

.goods-detail-box {
  margin-bottom: 10px;
  background-color: #fff;
  padding: 5px 8px; }

.goods-detail-box:last-child {
  margin-bottom: 0px; }

.goods-description {
  padding-top: 10px;
  padding-bottom: 10px; }

.goods-description-desc {
  line-height: 30px;
  text-indent: 0.5em;
  padding: 20px 10px;
  font-size: 14px;
  background-color: #fff; }

.goods-description img {
  width: 100%;
  margin: auto; }

.goods-footer {
  position: fixed;
  left: 0;
  width: 100%;
  bottom: 0;
  border-top: 1px solid #e2e2e2;
  height: 60px;
  max-height: 60px;
  background-color: #f9f9f9;
  text-align: center;
  z-index: 100;
  padding: 0px;
  display: block;
  width: 100%; }

@media (min-width: 769px) {
  .goods-footer {
    max-width: 640px;
    left: 50%;
    transform: translateX(-50%);
    -webkit-transform: translateX(-50%); } }
.goods-footer li {
  width: 15%;
  float: left;
  display: table;
  height: 60px;
  border-right: 1px solid #e2e2e2; }

.goods-footer li a {
  display: table-cell;
  vertical-align: middle; }

.goods-footer .goods-buy-block {
  background-color: #8db3ff;
  width: 35%;
  color: #fff; }

.goods-footer .goods-buy-block a {
  color: #fff; }

.goods-footer .goods-groupsale-block {
  background-color: #fe5430;
  width: 35%;
  color: #fff;
  font-size: 13px; }

.goods-footer .goods-groupsale-block a {
  color: #fff; }

.goods-footer .buy-price {
  font-size: 19px; }

.goods-footer .buy-text {
  font-size: 12px; }

.goods-footer .group-sales-price {
  font-size: 19px; }

.goods-footer .group-sales-text {
  font-size: 12px; }

.goods-footer a.disabled {
  background-color: #f5f5f5;
  color: #000000; }

.goods-footer .disabled {
  color: #000000; }

.goods-footer .iconfont {
  font-size: 22px; }

.goods-footer li.goods-offset-block {
  text-align: center;
  width: 70%;
  color: #fe5430;
  background-color: #f5f5f5; }

.goods-offset-block span {
  display: table-cell;
  vertical-align: middle;
  color: #fe5430;
  font-size: 18px; }

/*产品详情*/
.buy-box-content {
  padding: 10px;
  position: relative; }

.sku-img-info {
  min-height: 80px; }

.buy-box-content .img {
  position: absolute;
  width: 100px;
  left: 20px;
  top: -40px;
  max-height: 100px;
  overflow: hidden; }

.buy-box-content .img img {
  width: 100%; }

.buy-box-content .info {
  width: 100%;
  padding-left: 140px;
  padding-right: 20px; }

.buy-box-content .info .name {
  color: #444444;
  font-size: 12px;
  max-height: 54px;
  line-height: 18px;
  overflow: hidden;
  -webkit-line-clamp: 3;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  text-overflow: ellipsis; }

.buy-box-content .info .price {
  color: #fe5430;
  font-size: 17px; }

.buy-box-content .sprice-price {
  color: #444; }

.buy-box-content .info .s-price {
  text-decoration: line-through;
  color: #999;
  font-size: 12px; }

.sku-attributes-list {
  max-height: 300px;
  overflow: auto; }

@media (min-height: 768px) {
  .sku-attributes-list {
    max-height: 400px;
    overflow: auto; } }
@media (max-height: 500px) {
  .sku-attributes-list {
    max-height: 300px;
    overflow: auto; } }
@media (max-height: 400px) {
  .sku-attributes-list {
    max-height: 200px;
    overflow: auto; } }
@media (max-height: 300px) {
  .sku-attributes-list {
    max-height: 100px;
    overflow: auto; } }
@media (max-height: 500px) {
  .group-list-block {
    max-height: 200px;
    overflow: auto; }

  .buy-box-content .img {
    position: absolute;
    width: 60px;
    left: 20px;
    top: 10px; }

  .buy-box-content .info {
    padding-left: 80px; }

  .qty-box {
    margin-bottom: 30px; } }
@media (max-height: 400px) {
  .group-list-block {
    max-height: 250px;
    overflow: auto; }

  .buy-box-content .img {
    position: absolute;
    width: 40px;
    left: 20px;
    top: 40px; }

  .buy-box-content .info {
    padding-left: 60px; } }
@media (max-height: 300px) {
  .group-list-block {
    max-height: 150px;
    overflow: auto; } }
@media (max-height: 200px) {
  .group-list-block {
    max-height: 100px;
    overflow: auto; } }
@media (max-height: 100px) {
  .group-list-block {
    max-height: 50px;
    overflow: auto; } }
.attributes-item {
  margin-bottom: 10px;
  border-bottom: 1px solid #e2e2e2; }

.attributes-item:last-child {
  border-bottom: none; }

.attributes-item .title {
  margin-bottom: 10px; }

.attributes-item .attributes-value-item {
  display: inline-block;
  background-color: #f0f0f0;
  padding: 5px;
  border-radius: 8px;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  margin-right: 10px;
  margin-bottom: 10px;
  cursor: pointer;
  font-size: 12px; }

.attributes-item .attributes-value-item.select {
  background-color: #ff9b86;
  color: #ffffff; }

.attributes-item .attributes-value-item.disabled {
  background-color: #f5f5f5;
  color: #dddddd; }

.qty-box {
  margin-bottom: 100px; }

.qty-box .text {
  float: left; }

.qty-box .input {
  float: right; }

.qty-box .sku-number-reduce {
  display: inline-block;
  margin-right: -3px;
  height: 34px;
  line-height: 34px;
  background-color: #f0f0f0;
  width: 44px;
  vertical-align: top;
  font-size: 22px;
  text-align: center;
  cursor: pointer; }

.qty-box input {
  display: inline-block;
  vertical-align: top;
  width: 44px;
  height: 34px;
  line-height: 34px;
  margin-right: -3px;
  text-align: center;
  padding: 0px 2px;
  border-radius: 0px;
  -moz-border-radius: 0px;
  -webkit-border-radius: 0px; }

.btn-box {
  position: absolute;
  bottom: 0px;
  width: 100%;
  left: 0px; }

.btn-box .btn {
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px;
  font-size: 16px; }

/*.sake {
    -webkit-animation: alarm-2 0.02s infinite;
    animation: alarm-2 0.02s infinite;
}*/
@-webkit-keyframes alarm-2 {
  from {
    -webkit-transform: rotate(10deg); }
  to {
    -webkit-transform: rotate(-10deg); } }
@keyframes alarm-2 {
  from {
    transform: rotate(10deg); }
  to {
    transform: rotate(-10deg); } }
@keyframes shake_box {
  0% {
    transform: translate(0px, 0px) rotate(0deg); }
  20% {
    transform: translate(1.5px, -2.5px) rotate(-1.5deg); }
  40% {
    transform: translate(-2.5px, 0.5px) rotate(-0.5deg); } }
@-ms-keyframes shake_box {
  0% {
    -ms-transform: translate(0px, 0px) rotate(0deg); }
  20% {
    -ms-transform: translate(1.5px, -2.5px) rotate(-1.5deg); }
  40% {
    transform: translate(-2.5px, 0.5px) rotate(-0.5deg); } }
.shake {
  display: inline-block;
  -webkit-animation-name: shake_box;
  -ms-animation-name: shake_box;
  animation-name: shake_box;
  -webkit-animation-duration: 120ms;
  -ms-animation-duration: 120ms;
  animation-duration: 120ms;
  -webkit-animation-timing-function: ease-in-out;
  -ms-animation-timing-function: ease-in-out;
  animation-timing-function: ease-in-out;
  -webkit-animation-delay: 0s;
  -ms-animation-delay: 0s;
  animation-delay: 0s;
  -webkit-animation-play-state: running;
  -ms-animation-play-state: running;
  animation-play-state: running;
  -webkit-animation-iteration-count: infinite;
  -ms-animation-iteration-count: infinite;
  animation-iteration-count: infinite; }

.goods-detail-block {
  background-color: #f0f0f0;
  padding: 10px 0px; }

.goods-detail-box {
  margin-bottom: 10px;
  background-color: #fff;
  padding: 5px 8px; }

.goods-detail-box:last-child {
  margin-bottom: 0px; }

.goods-detail-box .header {
  padding-top: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e2e2e2; }

.goods-detail-box .title {
  color: #444444;
  font-weight: bold;
  font-size: 14px; }

.goods-detail-box .view-more {
  float: right;
  color: #444444;
  cursor: pointer; }

.goods-detail-box .view-more a {
  display: block;
  padding: 5px; }

.goods-review-item {
  padding: 10px;
  border-bottom: 1px solid #e2e2e2; }

.goods-review-item:last-child {
  border-bottom: none; }

.goods-review-item .avatar {
  display: inline-block;
  vertical-align: middle;
  width: 40px;
  max-height: 40px;
  overflow: hidden;
  margin-right: 5px; }

.goods-review-item .avatar img {
  width: 100%; }

.goods-review-item .username {
  display: inline-block;
  vertical-align: middle;
  color: #444444;
  font-size: 12px; }

.goods-review-item .time {
  float: right;
  color: #999999;
  font-size: 12px; }

.goods-review-item .time span {
  display: inline-block;
  vertical-align: middle; }

.goods-review-item .content {
  padding-top: 10px;
  line-height: 18px;
  color: #444444; }

.goods-review-item .spec {
  margin-top: 10px; }

.goods-review-item .spec-item {
  display: inline-block;
  color: #999999;
  font-size: 12px; }

.goods-review-image-list {
  margin-top: 10px; }

.goods-review-image-item {
  display: inline-block;
  vertical-align: middle;
  width: 120px;
  padding-right: 10px;
  margin-right: -4px;
  max-width: 33.3%; }

.goods-review-image-item img {
  width: 100%; }

.is-wish .wish-icon {
  color: #f00; }

/*首页*/
.site-header-box {
  background-color: #ffffff;
  height: 49px;
  padding: 0px 60px 0px 10px;
  position: relative; }

.site-header-box .search-box {
  top: 7.5px;
  line-height: 32px; }

.site-header-box .search-box input {
  color: #444;
  height: 32px; }

.site-header-box .app {
  position: absolute;
  right: 5px;
  top: 12.5px;
  padding: 4px;
  border-radius: 25px;
  -moz-border-radius: 25px;
  -webkit-border-radius: 25px;
  background-color: #fe5430; }

.header-shop {
  padding-top: 5px;
  text-align: center; }

.header-mark {
  padding: 5px 10px;
  background-color: #ffeb3b;
  color: #0d90e3;
  top: 6.5px;
  position: absolute;
  border-radius: 25px;
  -webkit-border-radius: 5px; }

.header-mark .iconfont {
  color: #0d90e3;
  font-size: 24px;
  display: inline-block;
  vertical-align: middle; }

.header-mark a {
  color: #0d90e3; }

.header-s {
  position: absolute;
  right: 10px;
  bottom: 1px; }

.home-content-box {
  min-height: 400px; }

.site-cate-nav {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  white-space: nowrap;
  height: 44px;
  padding: 0px 5px;
  border: 1px solid #e2e2e2;
  -webkit-overflow-scrolling: touch; }

.home-cate-nav {
  top: -8px;
  position: relative;
  border: none;
  z-index: 3;
  color: #09d0ed; }

.site-cate-nav li {
  display: inline-block;
  text-align: center;
  margin-right: -5px;
  padding-right: 10px;
  padding-left: 10px;
  cursor: pointer;
  vertical-align: middle; }

@media (max-width: 414px) {
  .site-cate-nav li {
    font-size: 0.24rem; } }
.site-cate-nav li span {
  height: 40px;
  line-height: 40px;
  display: block;
  transition: 0.23s ease-out;
  -webkit-transition: 0.23s ease-out;
  -moz-transition: 0.23s ease-out; }

.home-cate-nav li a {
  color: #ffffff; }

.site-cate-nav li.current span {
  border-bottom: 2px solid #FF9800; }

.cate-nav-child-box {
  background-color: #ffffff;
  padding: 10px; }

.cate-nav-child-item {
  display: inline-block;
  vertical-align: top;
  margin-right: -4px;
  margin-bottom: 20px;
  width: 20%;
  padding-right: 10px; }

.cate-nav-child-item .img {
  width: 100%; }

.cate-nav-child-item .lazy {
  padding-bottom: 100%;
  height: 0px;
  min-height: unset;
  overflow: hidden; }

.cate-nav-child-item img {
  width: 100%; }

.cate-nav-child-item .name {
  text-align: center;
  padding-top: 5px; }

.cate-nav-child-item .more_img {
  display: table;
  width: 100%;
  height: 1rem; }

.cate-nav-child-item .more_img_icon {
  display: table-cell;
  width: 44px;
  height: 44px;
  vertical-align: middle;
  text-align: center;
  background-image: url(../images/cate.png);
  background-repeat: no-repeat;
  background-position: center center;
  margin: auto; }

.cate-product-box {
  margin-top: 20px; }

.site-wrapper {
  margin: 20px 0px; }

.site-wrapper-title {
  font-size: 16px;
  font-weight: 600;
  color: #444444;
  margin-bottom: 20px;
  text-align: center; }

.platform li {
  float: left;
  width: 50%;
  padding-right: 10px;
  margin-bottom: 20px; }

.platform li a {
  display: block;
  height: 200px;
  padding: 0 32px;
  text-align: center;
  background: #fff;
  border: 2px solid #FFF;
  transition: transform 0.25s ease;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  -moz-box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.1);
  box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.1);
  border-color: #F3F3F3\9; }

.platform .ctr {
  width: 80px;
  height: 80px;
  padding: 20px;
  margin: 5px auto 0;
  background: #FAFAFA;
  -moz-border-radius: 50%;
  -webkit-border-radius: 50%;
  border-radius: 50%; }

.platform .tit {
  margin-top: 10px;
  font-size: 18px;
  color: #333;
  line-height: 1.4;
  font-weight: 700; }

.platform .txt {
  color: #999999;
  font-size: 12px;
  margin-top: 29px;
  line-height: 20px; }

.platform .icon-idcard {
  font-size: 30px;
  color: #FF9800; }

.platform .icon-idcard {
  font-size: 40px;
  color: #5bc0de; }

.platform .icon-article {
  font-size: 40px;
  color: #5eb95e; }

.site-i {
  padding-top: 0px;
  background-color: #ffffff; }

.site-pro {
  background-color: #ffffff; }

.site-pro-header {
  color: #f6f907;
  font-weight: 600;
  font-size: 16px;
  text-align: center;
  z-index: 2;
  position: relative; }

.site-pro-list {
  margin-top: -16px;
  padding: 0px 5px; }

.site-pro-list li {
  float: left;
  width: 25%;
  border: 1px solid #e2e2e2;
  padding: 0px 0px 8px 0px; }

.site-pro-list li .item-box {
  padding: 0px 3px; }

.site-pro-list li:last-child .item-box {
  margin-right: 0px; }

.site-pro-list li .img {
  position: relative;
  width: 100%;
  height: 0px;
  padding-bottom: 100%;
  overflow: hidden; }

.site-pro-list li img {
  display: block;
  width: 100%; }

.site-pro-list li .price {
  color: #fe5430;
  margin-top: 3px;
  font-size: 16px;
  font-weight: bold;
  line-height: 14px; }

.site-pro-list li .sprice {
  padding: 0px 2px;
  font-size: 12px;
  color: #999999;
  line-height: 14px;
  text-decoration: line-through; }

@media (max-width: 414px) {
  .site-pro-list li .price {
    font-size: 0.28rem; }

  .site-pro-list li .sprice {
    font-size: 0.24rem; } }
.site-i-item {
  width: 50%;
  text-align: center;
  display: inline-block;
  vertical-align: middle;
  margin-right: -4px; }

.site-i-box {
  position: relative; }

.site-i-item img {
  display: inline-block;
  vertical-align: middle; }

.site-i-item .text {
  position: absolute;
  font-size: 22px;
  color: #fc4a26;
  bottom: 0px;
  right: -40px; }

.site-i-item .wtext {
  right: -40px;
  bottom: -7px; }

.site-i-item .mtext {
  right: -64px;
  bottom: -7px; }

/*首页结束*/
/*搜索*/
.mobile-search-header {
  padding: 6px 10px 6px 10px; }

.mobile-header-block {
  position: relative; }

.mobile-header-search {
  position: absolute;
  width: 100%;
  padding: 6px 10px; }

.mobile-header-search .search-icon {
  position: absolute;
  font-size: 24px;
  top: 9px;
  left: 15px; }

.mobile-header-search input {
  width: 100%;
  height: 30px;
  font-size: 12px;
  background-color: #eeeeee;
  padding-left: 30px; }

.site-search-box {
  padding: 5px 10px; }

.site-search-block {
  position: relative;
  padding-right: 70px; }

.site-search-block .search-icon {
  position: absolute;
  font-size: 24px;
  top: 9px;
  left: 5px; }

.site-search-block input {
  padding-left: 30px; }

.site-search-block .close {
  position: absolute;
  right: 10px;
  top: 0px;
  height: 40px;
  line-height: 40px; }

.trending-search {
  padding: 20px 0px 10px 0px; }

.trending-search h2 {
  margin-bottom: 20px; }

.trending-search li {
  display: inline-block;
  padding: 4px;
  border-radius: 4px;
  -moz-border-radius: 4px;
  -webkit-border-radius: 4px;
  margin-right: 20px;
  background: #eeeeee;
  margin-bottom: 20px; }

.search-history {
  padding: 20px 0px; }

.search-history h2 {
  margin-bottom: 20px; }

.search-history li {
  display: block;
  padding: 5px;
  border-bottom: 1px solid #e2e2e2;
  position: relative;
  cursor: pointer; }

.search-history li a {
  display: block; }

.search-history li:first-child {
  border-top: 1px solid #e2e2e2; }

.search-history li .remove-link {
  position: absolute;
  right: 20px;
  color: #666666;
  top: 50%;
  margin-top: -14px;
  font-size: 20px; }

.clear-history-box {
  text-align: center;
  padding: 10px 0px;
  color: #FF9800; }

.clear-history-box a:hover {
  text-decoration: underline;
  color: #FF9800; }

.cate-search {
  position: fixed;
  background-color: #fff;
  z-index: 2;
  width: 100%;
  max-width: 640px; }

.site-category-block .search {
  position: relative;
  top: 0px;
  left: 0px;
  padding-left: 10px;
  padding-right: 10px;
  width: 100%; }

.cate-title .search {
  position: absolute; }

.search-box {
  position: relative;
  height: 34px;
  line-height: 34px;
  font-size: 14px;
  padding: 0 5px;
  background-color: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 5px;
  padding-left: 30px; }

.search-box input {
  width: 100%; }

.search-box i {
  position: absolute;
  font-size: 20px;
  top: 0px;
  left: 5px;
  color: #777; }

/*搜索*/
.cate_slide {
  margin-top: 20px;
  width: 100%; }

.cate-title {
  font-size: 18px;
  font-weight: 600;
  color: #222;
  background-color: #fff;
  position: relative;
  padding: 10px; }

.cate_slide_list {
  text-align: center;
  margin: 30px auto; }

.cate_slide_list li {
  display: inline-block;
  margin: 10px;
  animation: slideLeft .6s ease;
  -webkit-animation: slideLeft .6s ease;
  -moz-webkit-animation: slideLeft .6s ease;
  -ms-animation: slideLeft .6s ease;
  -o-animation: slideLeft .6s ease; }

.cate_slide_list li a {
  height: 34px;
  line-height: 34px;
  transition: all ease 0.5s;
  -webkit-transition: all ease 0.5s;
  -moz-transition: all ease 0.5s;
  -o-transition: all ease 0.5s;
  border: 1px solid #e2e2e2; }

.cate_slide_list li:hover a {
  color: #FF9800; }

/*分类页*/
.site-category-block {
  background-color: #ffffff; }

.site-category-aside {
  width: 24%;
  max-height: 580px;
  padding-bottom: 80px;
  overflow-y: scroll;
  z-index: 2;
  position: fixed;
  -webkit-overflow-scrolling: touch; }

@media (max-width: 769px) {
  .site-category-aside.fix {
    position: fixed;
    left: 0px;
    top: 0px; } }
.site-category-children {
  display: none; }

.site-category-children:first-child {
  display: block; }

.site-category-children.current {
  display: block; }

.site-category-side-box ul {
  background-color: #f9f9f9;
  padding: 0px 1px; }

.site-category-side-item {
  width: 100%;
  padding: 10px 8px;
  background-color: #f9f9f9;
  border-bottom: 1px solid #eeeeee;
  font-size: 14px;
  cursor: pointer;
  color: #666666;
  line-height: 18px;
  display: flex;
  display: -webkit-box;
  display: -webkit-flex;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  align-items: center;
  transition: 0.23s ease-out;
  -webkit-transition: 0.23s ease-out;
  -moz-transition: 0.23s ease-out;
  overflow: hidden; }

.site-category-side-item.current {
  color: #fe5430;
  background-color: #fff;
  border-left: 2px solid #fe5430; }

.site-category-list {
  padding-left: 26.6666%;
  padding-right: 20px;
  width: 100%;
  min-height: 400px; }

.category-box-header {
  padding-bottom: 12px; }

.category-children-item {
  display: inline-block;
  vertical-align: top;
  width: 50%;
  margin-bottom: 20px;
  padding-right: 3px;
  margin-right: -4px;
  animation: slideright .6s ease;
  -webkit-animation: slideright .6s ease;
  -moz-animation: slideright .6s ease;
  -ms-animation: slideright .6s ease;
  -o-animation: slideright .6s ease; }

@media (max-width: 414px) {
  .category-children-item {
    width: 100%; } }
.category-children-item:ntn-child(3n) {
  padding-right: 0px; }

.category-children-item .img {
  height: 150px;
  position: relative;
  overflow: hidden;
  background-color: #f5f5f5; }

.category-children-item img {
  width: 100%;
  min-height: 100px;
  vertical-align: middle; }

.category-children-item .name {
  position: absolute;
  bottom: 0px;
  background-color: rgba(0, 0, 0, 0.5);
  padding: 10px 0px;
  text-align: center;
  width: 100%;
  color: #fff; }

.site-cate-fixed {
  position: fixed;
  z-index: 10;
  background: #fff; }

.site-cate-fixed-box:after {
  clear: both;
  content: ' ';
  display: block;
  height: 44px; }

@media (min-width: 769px) {
  .site-cate-fixed {
    max-width: 640px;
    left: 50%;
    transform: translateX(-50%);
    -webkit-transform: translateX(-50%); } }
@media (min-width: 769px) {
  .site-category-aside {
    width: 210px; }

  .site-category-list {
    padding-left: 220px; } }
.site-category-list .list-item {
  padding: 10px 5px;
  border-bottom: 1px solid #eeeeee;
  margin-top: 0px;
  margin-bottom: 0px; }

.site-category-list .list-item .info-box {
  padding-right: 5px;
  padding-left: 100px; }

.site-category-list .info-block {
  margin-top: 10px;
  font-size: 12px;
  color: #999999; }

.site-category-list .list-item .img {
  width: 80px;
  min-height: 40px; }

.site-category-list .list-item .img img {
  width: 80px; }

/*分类页结束*/
/*店铺产品列表*/
.store-product-box {
  margin-top: 10px; }

.store-product-item {
  display: block;
  width: 100%;
  margin-bottom: 5px;
  position: relative;
  min-height: 140px;
  background-color: #fff;
  padding: 5px; }

.store-product-item .image {
  float: left;
  width: 21%;
  margin-right: 2%;
  z-index: 2;
  border: 1px solid #eeeeee; }

.store-product-item .image img {
  width: 100%; }

.store-product-item .info {
  float: left;
  width: 77%; }

.store-product-item .market_price {
  text-decoration: line-through; }

.store-product-item .name {
  line-height: 18px;
  max-height: 35px;
  overflow: hidden;
  font-size: 14px;
  color: #222; }

@media (max-width: 414px) {
  .store-product-item .name {
    font-size: 0.28rem; } }
.store-product-item .price-info {
  margin-top: 5px;
  line-height: 18px; }

.store-product-item .price {
  color: #ff9800;
  font-size: 15px;
  font-weight: 400;
  margin-right: 10px; }

.store-product-item .info-box {
  margin-top: 5px; }

.store-product-item .reserve_number {
  color: #fe5430; }

.store-product-item .view_number {
  color: #00f; }

.store-product-item .info-button {
  text-align: right;
  margin-top: 5px; }

.store-product-item .info-button a {
  color: #00f;
  padding: 5px; }

/*店铺产品列表*/
/*列表页*/
.site-product-box {
  padding: 0px 1px; }

.product-list-box {
  padding: 10px 0px; }

.product-grid {
  margin-bottom: 20px;
  padding: 10px;
  border: 1px solid #cccccc; }

.product-list {
  font-size: 0px;
  padding: 10px 0px 5px 5px; }

.product-row {
  vertical-align: top;
  display: inline-block;
  width: 49%;
  margin-bottom: 10px;
  margin-right: 1%;
  border: 1px solid #e2e2e2; }

.product-row:nth-child(2n) {
  margin-right: 0%; }

.product-item-box {
  background-color: #ffffff;
  padding-bottom: 10px; }

.product-item .lazy {
  width: 100%;
  height: 0;
  padding-bottom: 100%;
  min-height: unset;
  overflow: hidden;
  background-color: #f5f5f5; }

.product-item img {
  width: 100%; }

.product-item .info {
  margin-top: 10px;
  padding: 0px 5px; }

.product-item .name {
  line-height: 18px;
  max-height: 35px;
  overflow: hidden;
  font-size: 12px;
  color: #444444; }

.product-item .price-info {
  margin-top: 10px;
  line-height: 18px; }

.product-item .price {
  color: #f00;
  font-size: 16px;
  font-weight: 400;
  margin-right: 5px; }

.product-item .s-price {
  color: #999;
  font-size: 13px;
  font-weight: 400;
  margin-right: 5px;
  text-decoration: line-through; }

@media (max-width: 414px) {
  .product-item .price {
    font-size: 0.3rem; }

  .product-item .s-price {
    font-size: 0.22rem; } }
.product-item .sold {
  color: #666;
  font-size: 12px;
  margin-right: 2px; }

.product-row .sold {
  float: right; }

.product-grid .btn {
  float: right;
  height: 34px;
  line-height: 34px; }

.product-row .cibox {
  position: absolute;
  right: 10px;
  top: 0px;
  z-index: 3; }

.product-cripple {
  border-radius: 50%;
  width: 20px;
  height: 20px;
  line-height: 20px;
  text-align: center;
  color: #fff;
  font-size: 12px;
  display: block;
  background: #f00;
  -webkit-animation-name: 'cripple';
  /*动画属性名，也就是我们前面keyframes定义的动画名*/
  -webkit-animation-duration: 1s;
  /*动画持续时间*/
  -webkit-animation-timing-function: ease;
  /*动画频率，和transition-timing-function是一样的*/
  -webkit-animation-delay: 0s;
  /*动画延迟时间*/
  -webkit-animation-iteration-count: infinite;
  /*定义循环资料，infinite为无限次*/
  -webkit-animation-direction: alternate;
  /*定义动画方式*/ }

.self-product-cripple {
  color: #f7fa09;
  font-size: 16px; }

@keyframes cripple {
  0% {
    opacity: 0.35;
    transform: scale(0.1);
    -ms-transform: scale(0.1);
    /* IE 9 */
    -moz-transform: scale(0.1);
    /* Firefox */
    -webkit-transform: scale(0.1);
    /* Safari and Chrome */
    -o-transform: scale(0.1);
    /* Opera */ }
  100% {
    opacity: 1;
    transform: scale(1);
    -ms-transform: scale(1);
    /* IE 9 */
    -moz-transform: scale(1);
    /* Firefox */
    -webkit-transform: scale(1);
    /* Safari and Chrome */
    -o-transform: scale(1);
    /* Opera */ } }
@-webkit-keyframes cripple {
  0% {
    opacity: 0.35;
    transform: scale(0.1);
    -ms-transform: scale(0.1);
    /* IE 9 */
    -moz-transform: scale(0.1);
    /* Firefox */
    -webkit-transform: scale(0.1);
    /* Safari and Chrome */
    -o-transform: scale(0.1);
    /* Opera */ }
  100% {
    opacity: 1;
    transform: scale(1);
    -ms-transform: scale(1);
    /* IE 9 */
    -moz-transform: scale(1);
    /* Firefox */
    -webkit-transform: scale(1);
    /* Safari and Chrome */
    -o-transform: scale(1);
    /* Opera */ } }
@-moz-keyframes cripple {
  0% {
    opacity: 0.35;
    transform: scale(0.1);
    -ms-transform: scale(0.1);
    /* IE 9 */
    -moz-transform: scale(0.1);
    /* Firefox */
    -webkit-transform: scale(0.1);
    /* Safari and Chrome */
    -o-transform: scale(0.1);
    /* Opera */ }
  100% {
    opacity: 1;
    transform: scale(1);
    -ms-transform: scale(1);
    /* IE 9 */
    -moz-transform: scale(1);
    /* Firefox */
    -webkit-transform: scale(1);
    /* Safari and Chrome */
    -o-transform: scale(1);
    /* Opera */ } }
/*列表页结束*/
/*登录*/
.sign-panel .panel {
  display: none; }

.sign-panel .btn {
  width: 100%; }

.sign-panel-header .title {
  font-size: 18px; }

.sign-panel-header .info {
  margin-top: 5px;
  font-size: 14px; }

.entry-box {
  margin: 0px auto 30px auto;
  padding: 10px 20px 40px 20px; }

.register-tip {
  padding: 20px 0px;
  text-align: center; }

.entry-box-logo {
  padding-top: 30px;
  padding-bottom: 30px;
  width: 2.77rem;
  margin: 0px auto;
  overflow: hidden; }

.entry-box-logo img {
  width: 100%;
  height: auto; }

.entry-box .form-control {
  height: 40px;
  border: 1px solid #e2e2e2;
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px; }

.sign-panel-header {
  text-align: center;
  font-size: 18px;
  padding: 20px 0px; }

.forget-password {
  font-size: 12px;
  line-height: 40px;
  display: inline-block;
  text-decoration: none;
  vertical-align: top;
  color: #fff;
  text-decoration: underline; }

.sign-text {
  color: #ffffff;
  clear: both;
  text-align: center;
  font-size: 13px; }

.form-group-btn {
  margin-top: 20px; }

.sign-text a {
  text-decoration: underline;
  color: #ffffff; }

.social-login {
  margin: 40px auto 40px auto; }

.social-login span {
  display: inline-block;
  vertical-align: middle;
  font-size: 15px; }

.social-login .social-item {
  float: left;
  width: 44%;
  display: block; }

.social-login .s-or {
  float: left;
  width: 12%;
  text-align: center;
  margin-top: 14px; }

.social-item-block {
  border: 1px solid #e2e2e2;
  border-radius: 8px;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  display: block;
  background-color: #ffffff;
  text-align: center;
  height: 1rem;
  line-height: 1rem;
  color: #444444;
  width: 100%;
  position: relative; }

.social-item-block .ico {
  position: absolute;
  height: 24px;
  display: inline-block;
  vertical-align: middle;
  left: 16px;
  top: 50%;
  margin-top: -12px; }

.reset-password-box {
  padding: 20px 20px;
  background-color: #ffffff;
  margin-top: 20px; }

.reg-tip-box {
  padding: 20px;
  text-align: center; }

.reg-vip-box {
  background-color: #f5f5f5;
  padding: 20px;
  color: #222; }

.reg-vip-item {
  line-height: 25px; }

.vip-t {
  width: 120px;
  display: inline-block;
  color: #000;
  font-size: 14px; }

.vip-value {
  color: #000;
  font-size: 16px;
  margin-right: 20px; }

.vip-price-value {
  color: #FF9800; }

.vip-price-old {
  color: #444;
  text-decoration: line-through; }

.vip-desc-box {
  background-color: #f5f5f5;
  padding: 20px;
  color: #222;
  margin-top: 20px; }

.vip-desc-box {
  margin-top: 20px; }

.vip-desc-box li {
  line-height: 25px; }

.vip-desc-box .desc-title {
  margin-bottom: 10px; }

/*登录结束*/
/*支付页面*/
.checkout-box {
  margin-top: 10px; }

.checkout-panel {
  background-color: #ffffff;
  padding: 3px;
  margin-bottom: 5px; }

.checkout-panel-header {
  border-bottom: 1px solid #e2e2e2;
  padding-bottom: 5px;
  color: #444444;
  font-weight: 500;
  font-size: 14px; }

.payment-item:last-child {
  border-bottom: none; }

.payment-item {
  border-bottom: 1px solid #e2e2e2;
  padding: 5px 10px;
  position: relative;
  cursor: pointer; }

.payment-item:last-child {
  border-bottom: none; }

.payment-item .icon-weixin-zf {
  font-size: 24px;
  color: #1afa29; }

.payment-item span {
  display: inline-block;
  vertical-align: middle;
  margin-right: 10px; }

.payment-item .icon-wallet {
  font-size: 30px;
  color: #1296db; }

.payment-item .checkbox {
  display: block;
  position: absolute;
  top: 50%;
  margin-top: -12px;
  right: 20px;
  width: 24px;
  height: 24px;
  background-color: #e2e2e2;
  border-radius: 50%;
  text-align: center;
  font-size: 14px;
  color: #fff;
  padding-top: 3px;
  color: #ffffff; }

.payment-item.selected .checkbox {
  background-color: #FF9800; }

.payment-item:last-child {
  border-bottom: none; }

.checkot-footer {
  position: fixed;
  left: 0;
  width: 100%;
  bottom: 0;
  border-top: 1px solid #e2e2e2;
  height: 49px;
  background-color: #f9f9f9;
  text-align: center;
  z-index: 100;
  padding: 0px;
  display: block;
  width: 100%; }

@media (min-width: 769px) {
  .checkot-footer {
    max-width: 640px;
    left: 50%;
    transform: translateX(-50%);
    -webkit-transform: translateX(-50%); } }
.checkot-footer li {
  float: left;
  width: 50%;
  text-align: center;
  display: table;
  height: 49px; }

.checkot-footer li .box {
  display: table-cell;
  vertical-align: middle; }

.checkot-footer li .box {
  display: table-cell;
  vertical-align: middle; }

.checkot-footer .pay-btn-block {
  background-color: #FF9800;
  color: #ffffff; }

.checkot-footer .pay-btn-block a {
  font-size: 14px;
  color: #ffffff;
  display: block;
  width: 100%;
  height: 49px;
  line-height: 49px; }

.total-amount-info {
  font-size: 16px;
  color: #FF9800; }

.checkout-amount-detail {
  margin-top: 0px; }

.checkout-amount-item {
  padding: 2px 5px; }

.checkout-amount-item .value {
  color: #f00; }

.checkout-product-item {
  padding: 5px 0px 0px 0px; }

.checkout-product-item .img {
  float: left;
  width: 100px; }

.checkout-product-item .img img {
  width: 100%; }

.checkout-product-item .info {
  width: 100%; }

.checkout-product-item .info-box {
  padding-left: 110px;
  padding-right: 20px; }

.checkout-product-item .info-box .name {
  font-size: 12px;
  line-height: 18px;
  color: #444444; }

.checkout-product-item .price-info {
  margin-top: 1px;
  font-size: 14px;
  color: #fe5430; }

.checkout-product-item .sku-attr-info {
  margin-top: 5px; }

.checkout-product-item .sku-attr-item {
  line-height: 18px;
  font-size: 12px;
  margin-bottom: 5px; }

.checkout-product-item .sku-qty {
  margin-top: 5px;
  line-height: 18px;
  font-size: 13px; }

.checkout-product-item .qty-label {
  display: inline-block; }

.checkout-product-item .qty-text {
  display: inline-block; }

.checkout-product-item .expires-info {
  margin-top: 10px;
  font-size: 13px; }

.checkout-product-item .box-info {
  margin-top: 10px; }

.address-box .weight {
  font-size: 14px;
  font-weight: 600; }

.address-box {
  padding: 5px 5px 0px 5px;
  line-height: 20px;
  font-size: 12px;
  position: relative; }

.address-box .to {
  position: absolute;
  top: 50%;
  margin-top: -10px;
  right: 20px;
  font-size: 24px;
  color: #999999; }

/*支付页面结束*/
/*支付成功页面*/
.checkout-success-box {
  padding: 40px 0px;
  text-align: center;
  background-color: #ffffff;
  line-height: 30px; }

.checkout-success-box p {
  line-height: 25px; }

.checkout-success-box .icon-box {
  margin-bottom: 10px; }

.checkout-success-box .order-no {
  margin-top: 20px; }

.checkout-success-box .order-no .text {
  font-weight: 500;
  font-size: 14px;
  display: inline-block; }

.checkout-success-box .value {
  font-size: 14px;
  font-weight: bold;
  color: #000000;
  display: inline-block; }

.checkout-success-box .icon-success {
  font-size: 60px;
  color: #1296db; }

.checkout-success-share {
  padding: 20px 0px; }

.checkout-tip-info {
  font-size: 13px;
  color: #1296db;
  line-height: 25px; }

.checkout-tip-block {
  color: #999999;
  font-size: 13px;
  padding: 0px 10px; }

/*支付成功页面*/
/*支付回调*/
.checkout-state {
  position: relative;
  height: 420px; }

.checkout-state-box {
  position: absolute;
  left: 0px;
  right: 0px;
  top: 20px;
  width: 600px;
  margin: 0px auto;
  background-color: #ffffff;
  padding: 20px;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  line-height: 30px;
  font-size: 13px;
  max-width: 100%; }

.checkout-state-box .result-content {
  margin-top: 20px; }

.checkout-state-box .state-links {
  margin: 20px 0px; }

.checkout_success .share-block {
  background-color: #ffffff; }

/*支付回调*/
/*用户中心*/
.account-setting-panel {
  background-color: #ffffff;
  padding: 10px; }

.account-warp {
  background-color: #f0f0f0;
  margin-bottom: 0px; }

.account-panel {
  margin: 10px 0px;
  background-color: #ffffff; }

.account-panel-header {
  padding: 10px;
  border-bottom: 1px solid #e2e2e2; }

.account-panel-header .pull-right {
  float: right; }

.account-panel-header .icon-to-right {
  font-size: 20px; }

.account-panel-content {
  padding: 10px 0px; }

.account-panel-box {
  padding: 10px; }

.account-menu {
  margin-bottom: 20px; }

.list-group {
  padding: 0px 10px;
  background-color: #ffffff; }

.list-group a {
  display: block; }

.list-group-item {
  padding: 10px 0px;
  position: relative;
  border-bottom: 1px solid #e2e2e2; }

.list-group-item:hover a {
  color: #FF9800; }

.list-group-item .to {
  position: absolute;
  right: 20px; }

.order-status-item {
  float: left;
  width: 20%;
  text-align: center;
  margin-bottom: 10px; }

.order-status-item .iconfont {
  position: relative;
  font-size: 24px; }

.order-status-item:hover a {
  color: #fe5430; }

.order-status-item .number {
  position: absolute;
  right: -15px;
  top: -11px;
  width: 20px;
  height: 20px;
  line-height: 20px;
  border-radius: 50%;
  border-radius: 50%;
  border-radius: 50%;
  background-color: #fe5430;
  color: #fff;
  font-size: 10px;
  overflow: hidden; }

.order-status-item .number_1, .order-status-item .number_2 {
  width: 18px;
  height: 18px;
  top: -5px;
  padding: 0px; }

.menu-box-list {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  white-space: nowrap;
  background-color: #ffffff;
  height: 44px;
  padding: 0px 10px;
  -webkit-overflow-scrolling: touch; }

.menu-box-item {
  display: inline-block;
  text-align: center;
  margin-right: -5px;
  cursor: pointer; }

.menu-box-item a {
  padding-right: 15px;
  padding-left: 15px;
  height: 40px;
  line-height: 40px;
  display: block;
  transition: 0.23s ease-out;
  -webkit-transition: 0.23s ease-out;
  -moz-transition: 0.23s ease-out; }

.menu-box-item.current a {
  color: #FF9800; }

.account-content {
  margin-top: 20px;
  background-color: #ffffff;
  padding: 10px; }

.account-home-info {
  padding: 10px 10px;
  background-color: #fff;
  position: relative; }

.avatar-info {
  display: inline-block;
  width: 80px;
  max-height: 120px;
  -webkit-border-radius: 50%;
  border-radius: 50%;
  position: relative;
  margin-right: 5px; }

.avatar-info form {
  width: 80px;
  height: 80px; }

.account-home-info .avatar-info img {
  width: 100%;
  height: 80px; }

.account-home-info .username {
  display: inline-block;
  vertical-align: top;
  overflow: hidden;
  line-height: 25px; }

.account-home-info .name {
  font-size: 12px; }

.account-home-info p {
  font-size: 12px;
  color: #666;
  margin-top: 10px; }

.account-home-info .vip-info {
  float: right;
  text-align: right;
  max-width: 80px; }

@media (max-width: 414px) {
  .account-home-info .username {
    max-width: 280px; } }
@media (max-width: 360px) {
  .account-home-info .username {
    max-width: 240px; } }
@media (max-width: 370px) {
  .account-home-info .avatar-info {
    width: 60px;
    height: 60px; }

  .account-home-info .avatar-info form {
    width: 60px;
    height: 60px; }

  .account-home-info .avatar-info img {
    width: 60px;
    height: 60px; } }
@media (max-width: 340px) {
  .account-home-info .username {
    max-width: 160px; }

  .vip_end_date {
    display: block; } }
.account-home-info .btn-operate {
  padding: 3px 5px;
  border: 1px solid #e2e2e2;
  color: #FF9800;
  margin: 0px 5px; }

.account-box {
  padding: 10px;
  background-color: #ffffff;
  color: #444;
  margin-top: 10px; }

.account-box-item {
  float: left;
  width: 50%; }

.account-box .btn-operate {
  padding: 4px 5px;
  background-color: #fff;
  color: #FF9800;
  margin-left: 10px; }

@media (max-width: 360px) {
  .account-box .btn-operate {
    padding: 4px 3px; } }
@media (max-width: 320px) {
  .account-box .btn-operate {
    padding: 4px 1px; } }
.account-box p {
  line-height: 20px; }

.account-menu {
  margin-top: 10px; }

.account-menu .icon-box {
  display: inline-block;
  width: 40px;
  height: 40px;
  text-align: center;
  line-height: 40px;
  border-radius: 5px;
  margin-right: 10px;
  position: relative; }

.account-menu .iconfont {
  font-size: 24px;
  color: #fff; }

.account-menu .icon-box-wallet .iconfont {
  font-size: 26px; }

.icon-box-idcard {
  background-color: #1f8ff3; }

.icon-box-article {
  background-color: #8891eb; }

.icon-box-wallet {
  background-color: #29d242; }

.icon-box-setting {
  background-color: #f69369; }

.icon-box-address {
  background-color: #FF9800; }

.icon-box-link {
  background-color: #ffd11a; }

.icon-box-recommend {
  background-color: #ff5c5d; }

.icon-box-logout {
  background-color: #1f8ff3; }

.icon-box-custom {
  background-color: #ff6e40; }

.icon-box-qrcode {
  background-color: #f1b322; }

/*用户中心*/
/*微链接*/
.microlink-list {
  margin: 20px 0px;
  padding: 0px 10px;
  background-color: #fff;
  min-height: 400px; }

.microlink-item {
  display: inline-block;
  width: 25%;
  margin-right: -5px;
  padding: 0px 10px;
  margin-bottom: 20px;
  text-align: center;
  vertical-align: top; }

.microlink-list svg {
  width: 44px;
  height: 44px;
  margin-top: 10.5px;
  text-align: center; }

.microlink-item .name {
  margin-top: 10px;
  line-height: 18px;
  max-height: 18px;
  overflow: hidden;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis; }

.selectIconbox {
  width: 70px;
  height: 70px;
  text-align: center;
  border-radius: 50%;
  margin: 0 auto;
  background-color: #5ed2a5; }

.selectIconbox svg {
  width: 44px;
  height: 44px; }

.microlink-form-box {
  padding: 10px; }

.icon-list {
  max-height: 600px;
  overflow: auto; }

.icon-item {
  display: inline-block;
  padding-right: 10px;
  margin-bottom: 10px; }

.icon-item.select {
  background-color: #dddddd; }

.icon-item-box {
  border: 1px solid #e2e2e2; }

.icon-item-box svg {
  width: 44px;
  height: 44px; }

/*微链接*/
/*名片*/
.card-warp {
  display: block;
  width: 100%;
  background-repeat: repeat; }

.theme-hd {
  position: relative;
  height: 40px;
  z-index: 2; }

.musicBtn {
  position: absolute;
  top: 4px;
  right: 20px;
  width: 36px;
  height: 36px;
  z-index: 2; }

.musicBtn .music {
  display: inline-block;
  background-color: rgba(247, 247, 247, 0.3);
  border-radius: 50%;
  text-align: center;
  line-height: 36px;
  width: 36px;
  height: 36px; }

.musicBtn .music .iconfont {
  display: block;
  font-size: 24px;
  color: #444444;
  background-color: #f5f5f5;
  border-radius: 50%; }

.musicBtn.play {
  -webkit-animation: rotating 1.2s linear infinite;
  animation: rotating 1.2s linear infinite; }

@-webkit-keyframes rotating {
  from {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg); }
  to {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg); } }
@keyframes rotating {
  from {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg); }
  to {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg); } }
.card-nav-info li {
  width: 33.333333333333333%;
  float: left;
  height: 49px;
  display: table; }

.card-nav-info li a {
  color: #00f;
  display: block;
  text-align: center;
  line-height: 14px;
  display: table-cell;
  vertical-align: middle;
  border-right: 1px solid #e2e2e2; }

.card-nav-info .btn {
  color: #fff;
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px; }

/*背景列表*/
.background-item {
  float: left;
  width: 33.33%;
  padding: 0 5px;
  margin-bottom: 10px;
  position: relative; }

.background-item .img {
  max-height: 300px;
  overflow: hidden; }

.background-item img {
  width: 100%; }

/*背景列表*/
.card-warp {
  position: relative; }

.theme-bg {
  position: absolute;
  z-index: 1;
  width: 100%;
  left: 0;
  top: 0;
  height: 100%;
  background-color: #393a4c;
  background-repeat: no-repeat;
  background-position: top;
  background-size: cover; }

.edit-music-box {
  padding: 20px;
  width: 400px;
  max-width: 95%; }

/*名片*/
/*名片列表*/
.card-item {
  margin-top: 10px;
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  position: relative;
  background: #ffffff; }

.card-item-content {
  padding: 2px 0px; }

.card-item-header {
  padding: 5px 0px 10px 0px;
  border-bottom: 1px solid #e2e2e2;
  font-size: 13px; }

.card-item-header .list-no {
  font-size: 12px;
  color: #444444; }

.card-item-header .time-text {
  font-size: 12px;
  float: right;
  color: #999; }

.card-item-content {
  position: relative; }

.card-item .name {
  color: #444444;
  font-size: 14px;
  line-height: 18px; }

.card-item .checkedbox {
  display: inline-block;
  vertical-align: middle;
  margin-right: 30px; }

.card-item .checkbox {
  display: block;
  width: 24px;
  height: 24px;
  background-color: #e2e2e2;
  border-radius: 50%;
  text-align: center;
  font-size: 14px;
  color: #fff;
  padding-top: 3px;
  color: #ffffff; }

.card-item.selected .checkbox {
  background-color: #FF9800; }

.card-item .infobox {
  display: inline-block;
  vertical-align: middle; }

.infobox-btn {
  padding: 15px 0px 0px 0px; }

.card-item .info-block {
  margin-top: 10px;
  line-height: 20px;
  font-size: 13px;
  color: #666666; }

.card-item .info-block .value {
  color: #999; }

.card-view-nav li {
  float: left;
  width: 50%; }

.card-view-nav li a {
  display: block; }

/*list-item结束*/
/*钱包*/
.cpl-fund-home-count-view {
  background-color: #e02c36;
  padding-bottom: 15px;
  text-align: center; }

.cpl-fund-home .detailedbtn {
  padding: 15px 15px 10px 15px; }

.cpl-fund-home .detailedbtn a {
  float: right;
  border-radius: 3px;
  height: 20px;
  line-height: 20px;
  color: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.8);
  min-width: 50px;
  text-align: center; }

.cpl-fund-home-count-view .tit {
  color: #ffc8c8;
  font-size: 24px;
  text-align: center; }

.cpl-fund-home-count-view .tit i {
  color: #f9d30c;
  font-size: 30px;
  margin-right: 5px; }

.cpl-fund-home-count-view .tit {
  color: #ffc8c8;
  font-size: 24px;
  text-align: center; }

.cpl-fund-home-count-moneybox {
  text-align: center;
  padding: 15px 0;
  display: -webkit-inline-box;
  position: relative;
  text-align: center; }

.cpl-fund-home-count-money {
  color: #fff;
  font-size: 30px;
  vertical-align: baseline;
  line-height: 36px; }

.cpl-fund-home-count-view .viewbtn {
  color: #ffc9c9;
  padding: 10px 0 10px 10px;
  position: absolute;
  right: -23px;
  top: 11px; }

.cpl-fund-home-count-view .viewbtn i {
  font-size: 2rem; }

.cpl-fund-home-count-operate {
  height: 52px;
  background-color: #c42b2b; }

.cpl-fund-home-count-operate a:first-child {
  border-right: 1px solid #e02c36; }

.cpl-fund-home-count-operate a {
  color: #f4e0e0;
  font-size: 24px;
  height: 100%;
  line-height: 52px;
  text-align: center;
  width: 50%;
  float: left; }

.cpl-fund-home-count-operate a i.ic-withdraw {
  font-size: 30px; }

.cpl-fund-home-count-operate a i {
  color: #e7aaaa;
  font-size: 30px;
  margin-right: 5px; }

.cpl-fund-home-count-operate a {
  color: #f4e0e0;
  font-size: 24px;
  height: 100%;
  line-height: 52px;
  text-align: center; }

.cpl-fund-home-count-operate a {
  color: #f4e0e0;
  font-size: 24px;
  height: 100%;
  line-height: 52px;
  text-align: center; }

.cpl-fund-home-count-operate a i {
  color: #e7aaaa;
  font-size: 30px;
  margin-right: 5px; }

/*post*/
.post-warp {
  background-color: #ffffff;
  padding: 10px; }

.icon-upload {
  font-size: 24px;
  padding: 0px 5px; }

.post-save-nav li {
  width: 33.33%;
  float: left;
  display: table; }

.post-save-nav li a {
  color: #00f;
  width: 100%;
  text-align: center;
  line-height: 44px;
  display: table-cell;
  vertical-align: middle;
  border-right: 1px solid #e2e2e2; }

.post-save-nav .btn {
  color: #fff;
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px; }

.post-image-item {
  display: inline-block;
  vertical-align: top;
  width: 150px;
  margin-right: 10px;
  position: relative;
  text-align: center;
  border: 1px solid #e2e2e2; }

.icon-upload {
  display: block;
  background-color: #f5f5f5; }

.icon-upload input {
  opacity: 0;
  position: absolute;
  z-index: 1;
  left: 0px;
  height: 100%;
  width: 100%;
  top: 0px; }

.post-image-item .img {
  width: 150px;
  height: 100px;
  overflow: hidden; }

.post-image-item img {
  width: 150px; }

.post-list .list-item-content {
  padding: 5px; }

.post-item .img {
  max-height: 150px;
  overflow: hidden; }

.store-box {
  margin-top: 10px; }

.store-item {
  margin-bottom: 0px; }

.store-item .img {
  background-color: #f5f5f5;
  min-height: 100px;
  max-height: 400px;
  overflow: hidden; }

.store-item img {
  width: 100%; }

.store-item .name {
  margin-top: 10px;
  color: #444;
  font-size: 13px; }

.store-item .value {
  font-size: 14px; }

.store-item .u-time-box {
  margin-top: 5px;
  color: #444;
  font-size: 13px; }

.store-item .button {
  padding: 5px 15px;
  background-color: #FF9800;
  color: #fff; }

/*order*/
/*order-list*/
.order-list-box {
  margin-top: 5px; }

.order-item {
  width: 100%;
  padding: 10px;
  margin-bottom: 5px;
  position: relative;
  background: #ffffff; }

.order-item-content {
  padding: 5px 0px; }

.order-item-header {
  padding: 0px 0px 5px 0px;
  border-bottom: 1px solid #eeeeee; }

.order-item-header .order-no {
  font-size: 12px;
  color: #444444; }

.order-item-header .order-status-text {
  font-size: 12px;
  float: right;
  color: #f00; }

.order-item .img {
  float: left;
  width: 120px;
  min-height: 120px; }

.order-item .img img {
  width: 100%; }

.order-item .info {
  width: 100%; }

.order-item .info-box {
  padding-left: 140px;
  padding-right: 20px; }

.order-item .name {
  color: #444444;
  font-size: 12px;
  line-height: 18px; }

.order-item .price-info {
  line-height: 18px;
  color: #fe5430; }

.order-item .price {
  font-size: 14px;
  font-weight: 400;
  margin-right: 10px; }

.order-item .spec {
  color: #666666;
  line-height: 25px;
  font-size: 12px; }

.order-item .qty {
  color: #666666;
  line-height: 14px;
  font-size: 12px; }

.order-item .operate-box {
  padding: 10px 0px 0px 0px;
  clear: both;
  text-align: right;
  border-top: 1px solid #eeeeee; }

.order-item .expires-info {
  margin-top: 10px;
  color: #fe5430; }

.order-item .box-info {
  margin-top: 10px;
  line-height: 25px;
  font-size: 12px; }

.order-item .order-share-link.show {
  display: inline-block; }

.order-item .order-share-link.hide {
  display: none; }

@media (max-width: 360px) {
  .order-item .img {
    width: 90px; }

  .order-item .info-box {
    padding-left: 100px; } }
/*wish-list结束*/
/*order view*/
.order-view-warp {
  margin: 5px 0px; }

.order-view-panel {
  background-color: #ffffff;
  margin-bottom: 5px; }

.order-amount-item {
  line-height: 20px;
  padding: 3px 0px; }

.order-panel-header {
  padding: 2px 10px 5px 10px;
  border-bottom: 1px solid #e2e2e2; }

.order-panel-header .pull-right {
  float: right; }

.order-panel-header .order-status-text {
  font-size: 14px;
  color: #fe5430; }

.order-panel-content {
  padding: 5px;
  line-height: 20px; }

.order-panel-content .address-value {
  padding: 5px; }

.order-panel-content .operate-box {
  margin-top: 10px;
  text-align: right; }

.order-product-item {
  padding: 10px 0px;
  border-bottom: 1px solid #e2e2e2;
  margin-bottom: 5px; }

.order-product-item:last-child {
  border-bottom: none; }

.order-product-item .img {
  float: left;
  width: 120px;
  min-height: 120px; }

.order-product-item .img img {
  width: 100%; }

.order-product-item .info {
  width: 100%; }

.order-product-item .info-box {
  padding-left: 140px;
  padding-right: 20px; }

.order-product-item .name {
  font-size: 12px;
  line-height: 18px; }

.order-product-item .price-info {
  margin-top: 10px;
  line-height: 18px; }

.order-product-item .price-text {
  color: #fe5430; }

.order-product-item .spec {
  color: #777777;
  font-size: 12px;
  margin-top: 5px;
  line-height: 20px; }

.order-product-item .qty {
  color: #444444;
  font-size: 12px;
  margin-top: 5px; }

.order-review-warp {
  padding: 20px 0px; }

.order-product-review-item {
  background-color: #ffffff;
  margin-bottom: 20px;
  padding: 10px 10px; }

.rating-box {
  display: inline-block;
  vertical-align: middle; }

.rating-star li {
  background-image: url(../images/stars.png);
  background-repeat: no-repeat;
  width: 20px;
  height: 20px;
  cursor: pointer;
  background-size: auto 20px;
  float: left;
  margin-right: 10px;
  background-position: -26px 0px; }

.rating-star li.select {
  background-image: url(../images/stars.png);
  background-repeat: no-repeat;
  width: 20px;
  height: 20px;
  cursor: pointer;
  background-size: auto 20px;
  float: left;
  margin-right: 10px;
  background-position: 0px 0px; }

.order-product-review-item .rating-info {
  margin-top: 15px; }

.order-product-review-item .rating-text {
  margin-top: 10px;
  line-height: 25px;
  color: #444444; }

.order-product-review-item .reply-text {
  margin-top: 10px;
  color: #777777;
  font-size: 13px; }

.order-product-review-item .icon-photo {
  font-size: 30px; }

.upload-image-list {
  margin-top: 20px; }

.upload-review-image-item {
  display: inline-block;
  vertical-align: middle;
  width: 120px;
  padding-right: 15px;
  margin-right: -4px;
  max-width: 33.3%;
  margin-bottom: 10px; }

.review-image-item-box img {
  width: 100%; }

.review-image-item-box {
  position: relative;
  border: 1px solid #e2e2e2; }

.upload-review-image-item .remove {
  position: absolute;
  font-family: arial;
  font-size: 20px;
  font-weight: 700;
  color: #999999;
  text-decoration: none;
  right: -10px;
  z-index: 2;
  top: -15px;
  padding: 3px; }

.order-review-image-list {
  margin-top: 10px; }

.order-review-image-item {
  display: inline-block;
  vertical-align: middle;
  width: 120px;
  padding-right: 10px;
  margin-right: -4px;
  max-width: 33.3%; }

.order-review-image-item img {
  width: 100%; }

@media (max-width: 360px) {
  .order-product-item .img {
    width: 90px;
    min-height: 90px; }

  .order-product-item .info-box {
    padding-left: 100px; } }
/*order结束*/
/*地址*/
.layer-address-box .layerbox-content {
  background-color: #f0f0f0; }

.current-address-content {
  padding-right: 30px; }

.address-list-box {
  padding-top: 10px;
  padding-bottom: 10px; }

.address-list-item {
  background-color: #ffffff;
  padding: 5px 10px 20px 10px;
  margin-bottom: 10px;
  position: relative; }

.address-list-item .weight {
  font-size: 14px;
  font-weight: 600; }

.address-list-item .select-address {
  padding: 10px;
  position: absolute;
  top: 50%;
  margin-top: -12px;
  right: 20px; }

.address-list-item .checkbox {
  display: block;
  width: 24px;
  height: 24px;
  background-color: #e2e2e2;
  border-radius: 50%;
  text-align: center;
  font-size: 14px;
  color: #fff;
  padding-top: 3px;
  color: #ffffff; }

.address-list-item.selected .checkbox {
  background-color: #FF9800; }

.address-list-item .address-value {
  padding: 10px 30px 20px 0px;
  line-height: 25px; }

.address-list-item .edit-address {
  border: 1px solid #e2e2e2;
  background-color: #ffffff;
  padding: 5px; }

.address-list-item .operate-btn {
  border: 1px solid #e2e2e2;
  background-color: #ffffff;
  padding: 8px;
  margin-right: 5px; }

.address-form-box {
  padding: 10px;
  margin-bottom: 50px; }

.layer-footer-box {
  width: 100%;
  border-top: 1px solid #e2e2e2;
  height: 49px;
  background-color: #ffffff;
  text-align: center;
  z-index: 100;
  padding: 0px;
  display: block; }

.button-box input, .button-box a {
  display: block;
  height: 49px;
  line-height: 49px;
  text-align: center;
  color: #ffffff;
  width: 100%;
  background-color: #f2bc39; }

.address-form-box .form-group {
  margin-bottom: 15px; }

.add-address-box {
  margin-top: 10px; }

.add-address-box .add-address {
  padding: 5px 10px;
  font-size: 14px;
  border: 1px solid #e2e2e2; }

.add-address-box .add-address:hover {
  color: #fe5430; }

/*地址*/
/*页面布局结束*/
/*帮助页面*/
.help-contact-box {
  padding: 100px 0px;
  text-align: center;
  line-height: 25px; }

/*帮助页面*/
table {
  font-family: verdana,arial,sans-serif;
  font-size: 11px;
  color: #333333;
  border-width: 1px;
  border-color: #666666;
  border-collapse: collapse; }

table th {
  border-width: 1px;
  padding: 8px;
  border-style: solid;
  border-color: #666666;
  background-color: #dedede; }

table td {
  border-width: 1px;
  padding: 8px;
  border-style: solid;
  border-color: #666666;
  background-color: #ffffff; }

/*# sourceMappingURL=app.cs.map */
