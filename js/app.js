$(function() {
  // 現在のメニューのスタイル
  var url = window.location.pathname;
  $('.nav-login-menu a[href="' + url + '"] i').addClass("active fas");
  if ($('.nav-login-menu a[href="' + url + '"] i').length === 0) {
    if (url === "/pofori/createrPage.php") {
      $('.nav-login-menu a[href="/pofori/index.php"] i').addClass("active");
    } else {
      console.log(url);
      $('.nav-login-menu a[href="/pofori/mypage.php"] i').addClass(
        "active fas"
      );
    }
  }

  // 自ページ遷移制御
  $(".my-item.pofori .creater-page").on("click", function() {
    return false;
  });

  // トグルメニュー
  $(".js-toggle-sp-menu").on("click", function() {
    $(this).toggleClass("active");
    $(".js-toggle-sp-menu-target").toggleClass("active");
    // $(".js-toggle-opacity").toggleClass("active");
  });

  // マイページのPOFORI、FOVORITE切り替え
  $(".term.reactive, .term.active").on("click", function() {
    $(".term.active")
      .removeClass("active")
      .addClass("reactive");
    $(this)
      .removeClass("reactive")
      .addClass("active");
    return false;
  });

  //select切り替え
  $(".pofori-btn").on("click", function() {
    $(".my-item.pofori").fadeIn();
    $(".my-item.favo").hide();
  });
  $(".favorite-btn").on("click", function() {
    $(".my-item.pofori").hide();
    $(".my-item.favo").fadeIn();
  });

  //画像ライブプレビュー
  var $fileInput = $(".photo-input");
  $fileInput.on("change", function(e) {
    $(".choise-msg").hide();
    var file = this.files[0],
      $img = $(".prev-img"),
      fileReader = new FileReader();

    fileReader.onload = function(event) {
      $img.attr("src", event.target.result).show();
    };
    $(".prev-img.signup").css({ width: "80px", height: "80px" });
    $(".photo-area-pofori").css({ width: "100%" });
    $(".prev-img.pofori").css({ width: "100%", height: "auto" });

    fileReader.readAsDataURL(file);
  });

  // //フォームアニメーション
  // var $input = $('.input');
  // $input.focusin(function(){
  //     $(this).siblings('.focus-animation').css('width', '100.5%');
  // }).focusout(function(){
  //     $(this).siblings('.focus-animation').css('width', '0');
  // });

  //favorite機能
  $(document).on("click", ".favo-btn", function(e) {
    // $('.favo-btn').on('click', function(){
    $this = $(this);
    var poforiId = $this.siblings(".favo-data").data("pofori_id");
    $.ajax({
      type: "POST",
      url: "favorite.php",
      data: {
        pofori_id: poforiId
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log("ajax通信に失敗しました");
      },
      success: function(response) {
        console.log("ajax通信に成功しました");
      }
    })
      .done(function(data) {
        //errorの場合
        if (data === "error") {
          alert("ログインしなおしてください");
          $(document).off();
        }

        var num = $this.children(".favo-num").text(),
          count = parseInt(num);
        if (data === "countdown") {
          count--;
          $this.children(".favo-num").text(count);
          $this.children(".favo-icon").toggleClass("js-toggle-favo");
        } else if (data === "countUp") {
          count++;
          $this.children(".favo-num").text(count);
          $this.children(".favo-icon").toggleClass("js-toggle-favo");
        }
      })
      .fail(function() {
        console.log("失敗しました");
      });
  });

  //formバリデーション

  //name
  $(".js-input-name").on("keyup", function() {
    var $this = $(this),
      name_length = $this.val().length;

    //最大文字数チェック
    if (name_length > 20) {
      $this.siblings(".js-max-name").addClass("valid-active");
      $this.siblings(".area-msg").css("display", "none");
      $this.addClass("led-border");
    } else {
      $this.siblings(".js-max-name").removeClass("valid-active");
      $this.siblings(".area-msg").css("display", "block");
      $this.removeClass("led-border");
    }

    //name重複チェック
    var name = $this.val();
    $.ajax({
      type: "POST",
      url: "validName.php",
      data: {
        name: name
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log("ajax通信に失敗しました");
      }
    })
      .done(function(data) {
        if (data === "check") {
          $this.siblings(".js-valid-name").addClass("valid-active");
          $this.siblings(".area-msg").css("display", "none");
          $this.addClass("led-border");
        } else {
          $this.siblings(".js-valid-name").removeClass("valid-active");
          $this.removeClass("led-border");
          $this.siblings(".area-msg").css("display", "block");
        }
      })
      .fail(function() {
        console.log("失敗しました");
      });
  });

  //email
  $(".js-input-email").on("keyup", function() {
    var $this = $(this),
      email = $this.val();
    $.ajax({
      type: "POST",
      url: "validEmail.php",
      data: {
        email: email
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log("ajax通信に失敗しました");
      }
    })
      .done(function(data) {
        if (data === "check") {
          $this.siblings(".js-valid-email").addClass("valid-active");
          $this.siblings(".area-msg").css("display", "none");
          $this.addClass("led-border");
        } else {
          $this.siblings(".js-valid-email").removeClass("valid-active");
          $this.siblings(".area-msg").css("display", "block");
          $this.removeClass("led-border");
        }
      })
      .fail(function() {
        console.log("失敗しました");
      });
  });

  //check欄
  $(".js-check").on("change", function() {
    $(this)
      .siblings(".check-icon")
      .toggleClass("check-active");
  });

  //文字数カウント
  $(".input-textarea").on("keyup", function() {
    var validLength = 250 - $(this).val().length;

    if (validLength <= 10) {
      $(".js-count").text(validLength);
      $(".js-count").css("display", "block");

      if (validLength <= 0) {
        $(".js-count").addClass("count-area-active");
      } else {
        $(".js-count").removeClass("count-area-active");
      }
    } else {
      $(".js-count").hide();
    }
  });

  // POFORI削除の確認アラート
  var deleteClicked = false;
  $(".delete-pofori-btn").on("click", function(e) {
    if (deleteClicked) return;

    var result = confirm("本当に削除しますか？");

    if(!result) console.log('nonoo');
    if(!result) return false;
  });
});
