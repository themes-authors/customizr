module.exports = {
  options: {
    // compatibility: {
    //     properties: {
    //         spaceAfterClosingBrace: true
    //     }
    // }
  },
  dev_skin: {
    files: [
      {'<%= paths.front_css %>grey.min.css' : '<%= paths.front_css %>grey.css'}
    ]
  },
  dev_common: {
    files: [
      {'<%= paths.front_css %>tc_common.min.css' : '<%= paths.front_css %>tc_common.css'}
    ]
  },
  dev_main_c4: {
    files: [
      {'<%= paths.front_css4 %>style.min.css' : '<%= paths.front_css4 %>style.css'}
    ]
  },
  prod_front_c4: {
    expand: true,
    cwd: '<%= paths.front_css4 %>',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.front_css4 %>',
    ext: '.min.css'
  },
  prod_skins: {
    expand: true,
    cwd: '<%= paths.front_css %>',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.front_css %>',
    ext: '.min.css'
  },
  prod_common :{
    expand: true,
    cwd: '<%= paths.front_css %>',
    src: ['tc_common.css'],
    dest: '<%= paths.front_css %>',
    ext: '.min.css'
  },
  prod_common_rtl :{
    expand: true,
    cwd: '<%= paths.front_css %>rtl/',
    src: ['tc_common.css'],
    dest: '<%= paths.front_css %>rtl/',
    ext: '.min.css'
  },
  prod_admin_css: {
    expand: true,
    cwd: '<%= paths.admin_css %>',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.admin_css %>',
    ext: '.min.css'
  },
  prod_czr_css: {
    expand: true,
    cwd: '<%= paths.czr_assets %>/css',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.czr_assets %>/css',
    ext: '.min.css'
  },
  custom_skin : {
    expand: true,
    cwd: 'custom-skins/',
    src: ['*.css', '!*.min.css'],
    dest: 'custom-skins/',
    ext: '.min.css'
  },
};