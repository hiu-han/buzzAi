$(document).ready(function () {
  const NOWON_CLASSNAME = 'nowOn';

  /** USER > userInfo > 수정, 저장, 취소 ///// START */
  const sUserInfoHandler = {
    changeOn: function (clicker) {
      $(clicker).on('click', function () {
        $('.btn__wrap.content--foot').addClass(NOWON_CLASSNAME);
        $('.btn--convertible').addClass(NOWON_CLASSNAME);
        $('.item--convertible').addClass(NOWON_CLASSNAME);
        $('.input--modifiable').attr('readonly', false).css({
          'background-color': 'transparent',
          color: '#313131',
        });
        $('.select--modifiable').attr('disabled', false).css({
          'background-color': 'transparent',
          color: '#313131',
        });
      });
    },
    save: function (clicker) {
      $(clicker).on('click', function () {
        $('.btn__wrap.content--foot').removeClass(NOWON_CLASSNAME);
        $('.btn--convertible').removeClass(NOWON_CLASSNAME);
        $('.item--convertible').removeClass(NOWON_CLASSNAME);
        $('.input--modifiable').attr('readonly', true).css({
          'background-color': '#f8f8f8',
          color: '#888888',
        });
        $('.select--modifiable').attr('disabled', true).css({
          'background-color': '#f8f8f8',
          color: '#888888',
        });
      });
    },
  };
  sUserInfoHandler.changeOn('#userInfoChangeBtn');
  sUserInfoHandler.save('#userInfoSaveBtn');
  sUserInfoHandler.save('#userInfoCancelBtn');
  /** USER > userInfo > 수정, 저장, 취소 ///// END */

  /** 모달 핸들러 START */
  const sModHandler = {
    modOn: function (clicker, modalName) {
      $(clicker).on('click', function (e) {
        $(modalName).addClass(NOWON_CLASSNAME);
        e.preventDefault();
        $(`${modalName} .modal-bg`).on(
          'scroll touchmove mousewheel',
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }
        );
      });
    },
    modOff: function (clicker, modalName) {
      $(clicker).on('click', function (e) {
        console.log('clicked');
        $(modalName).removeClass(NOWON_CLASSNAME);
        e.preventDefault();
        $(`${modalName} .modal-bg`).off('scroll touchmove mousewheel');
      });
    },
  };
  sModHandler.modOn('#jobReferOpener', '#jobReferModal');
  sModHandler.modOff('.btn--modal-cls', '#jobReferModal');

  /** 모달 핸들러 END */

  /** 분류 탭 START **/

  const tabs = document.querySelectorAll('.tab__item');
  tabs.forEach(function (tab) {
    tab.addEventListener('click', tabSwitching);
  });

  function tabSwitching() {
    $('.tab__item').on('click', function () {
      $(this).addClass('nowOn').siblings('.tab__item').removeClass('nowOn');
    });
  }

  /** 분류 탭 END **/
});
