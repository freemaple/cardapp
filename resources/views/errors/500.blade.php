<!DOCTYPE html>
<html>
    <head>
        <title>Be right back.</title>
        <style>
            html, body {
                height: 100%;
            }
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family:  "Helvetica Neue", Helvetica, Arial, sans-serif;
            }
            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
                max-width: 80%
            }
            .content {
                text-align: center;
                display: inline-block;
                 font-size: 20px;
                line-height: 25px;
            }
            .control-group {
                margin-top: 10px;
            }
            .control-group a {
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                Whoops, looks like something went wrong.
            </div>
            <div class="control-group">
                <a class="" href="">Refresh</a> Or
                <a class="" href="/">{{ trans('view.words.view_other') }}</a>
        	</div>
        </div>
    </body>
</html>

