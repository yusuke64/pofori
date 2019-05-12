const defaultValue = document.querySelector(".search-input");

new Vue({
  el: ".search-area",
  data: {
    search: defaultValue.value,
    searchResultUsers: document.querySelector(".search-result-users"),
    searchResultPoforis: document.querySelector(".search-result-poforis"),
    resultValue: [],
  },
  // methods: {
  //   search:  function(){
  //     const value = document.querySelector('.search-input').value;

  //     console.log(value);
  //     if (!(document.querySelector(".empty-msg-area") === null)) {
  //       document.querySelector(".empty-msg-area").remove();
  //     }

  //     const searchResultUsers = this.searchResultUsers;
  //     const searchResultPoforis = this.searchResultPoforis;
  //     searchResultUsers.textContent = "";
  //     searchResultPoforis.textContent = "";
  //     if (value === "") return;

  //     let params = new URLSearchParams();
  //     params.append("data", value);

  //     axios.post("./searchAxios.php", params).then(response => {
  //       const resultUsresDatas = response.data["users"];
  //       const resultPoforisDatas = response.data["poforis"];

  //       if (!resultUsresDatas.length && !resultPoforisDatas.length) return;
  //       resultUsresDatas.forEach(user => {
  //         let image;
  //         if (user.prof_image) {
  //           image = user.prof_image;
  //         } else {
  //           image = "default.png";
  //         }

  //         searchResultUsers.insertAdjacentHTML(
  //           "afterbegin",
  //           `<form method="POST" action="createrPage.php">
  //             <input type="hidden" name="id" value="${user.id}">
  //             <button type="submit" class="search-result-user">
  //             <span class="search-result-image"><img src="./profImage/${image}"></span>
  //             <span class="search-result-name">${user.name}</span>
  //             </button>
  //           </form>`
  //         );
  //       });
  //       resultPoforisDatas.forEach(pofori => {
  //         searchResultPoforis.insertAdjacentHTML(
  //           "afterbegin",
  //           `<form method="POST" action="">
  //             <input type="hidden" name="search" value="${pofori.lang}">
  //             <button type="submit" class="search-result-pofori">
  //             <p class="search-result-pofori-lang">${pofori.lang}</p><i class="fas fa-arrow-circle-right"></i>
  //             </button>
  //           </form>`
  //         );
  //       });
  //     });
  //   }
  // }
  watch: {
    search(value) {
      if (!(document.querySelector(".empty-msg-area") === null)) {
        document.querySelector(".empty-msg-area").remove();
      }

      const searchResultUsers = this.searchResultUsers;
      const searchResultPoforis = this.searchResultPoforis;
      searchResultUsers.textContent = "";
      searchResultPoforis.textContent = "";
      if (value === "") return;

      let params = new URLSearchParams();
      params.append("data", value);

      axios.post("./searchAxios.php", params).then(response => {
        const resultUsresDatas = response.data["users"];
        const resultPoforisDatas = response.data["poforis"];

        if (!resultUsresDatas.length && !resultPoforisDatas.length) return;
        resultUsresDatas.forEach(user => {
          let image;
          if (user.prof_image) {
            image = user.prof_image;
          } else {
            image = "default.png";
          }

          searchResultUsers.insertAdjacentHTML(
            "beforeend",
            `<form method="POST" action="createrPage.php">
              <input type="hidden" name="id" value="${user.id}">
              <button type="submit" class="search-result-user">
              <span class="search-result-image"><img src="./profImage/${image}"></span>
              <span class="search-result-name">${user.name}</span>
              </button>
            </form>`
            );
          });
          resultPoforisDatas.forEach(pofori => {
            this.resultValue.forEach(val => {
              if(val.lang === pofori.lang) console.log(val);
            });
          this.resultValue.push(pofori);
          searchResultPoforis.insertAdjacentHTML(
            "beforeend",
            `<form method="POST" action="">
              <input type="hidden" name="search" value="${pofori.lang}">
              <button type="submit" class="search-result-pofori">
              <p class="search-result-pofori-lang">${pofori.lang}</p><i class="fas fa-arrow-circle-right"></i>
              </button>
            </form>`
          );
        });
      });
    }
  }
});
