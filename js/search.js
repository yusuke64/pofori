const searchValue = document.querySelector(".search-input");

new Vue({
  el: ".main",
  data: {
    search: searchValue.value,
    searchResultUsers: [],
    searchResultPoforis: [],
  },
  watch: {
    search(value) {

      if (value === ""){
        this.searchResultUsers = [];
        this.searchResultPoforis = [];
        return;
      }

      let params = new URLSearchParams();
      params.append("data", value);

      axios.post("./searchAxios.php", params).then(response => {

        this.searchResultUsers = response.data.users;
        this.searchResultPoforis = response.data.poforis;

      }).catch(error => {
        alert('エラーが発生しました。時間が経ってから再度お試しください。');
        console.log(error);
      });
    }
  },
  created: function() {
    if(!(dbUsersData === null)){
      const jsondata = JSON.parse(dbUsersData);
      this.searchResultUsers = jsondata;
    }
  },
  filters: {
    image: function(val) {

      let image;
      if (val) {
        image = val;
      } else {
        image = "default.png";
      }

      return "./profImage/" + image;
    }
  }
});
