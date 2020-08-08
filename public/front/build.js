({
    appDir: './',
    baseUrl: './scripts',
    dir: './production',
    modules: [
        {
            name: 'run'
        }
    ],
    fileExclusionRegExp: /^(r|build)\.js$/,
    optimizeCss: 'standard',
    removeCombined: true,
    paths: {
             'jquery': 'plugin/jquery.min',
            'base': 'module/base',
            'validate': 'plugin/jquery.validate.min',
            'AsyncForm': 'module/asyncForm',
            'loginReg': 'module/loginReg',
            'search': 'module/search',
            'jcarousellite': 'plugin/jcarousellite',
            'lazyload': 'plugin/jquery.lazyload.min',
            'mylayer': 'module/mylayer',
            'address': 'module/address',
            'design': 'module/design',
            'svg': 'plugin/svg.min',
            'ajaxForm': 'plugin/jquery.form.min',
            'wangEditor': 'wangEditor/js/wangEditor.min',
            'moment': 'daterangepicker/moment.min',
            'daterangepicker': 'daterangepicker/jquery.daterangepicker.min',
            'dropDown': 'module/dropDown',
        },
        shim: {
            'lazyload': {
                exports: 'lazyload',
                deps: ['jquery']
            },
            'validate': {
                exports: 'validate',
                deps: ['jquery']
            },
            'wangEditor': {
                exports: 'wangEditor',
                deps: ['jquery']
            }
        }
})