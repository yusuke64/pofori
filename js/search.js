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
        const resultUsresDatas = response.data.users;

        const resultPoforisDatas = response.data.poforis;

        this.searchResultUsers = resultUsresDatas;
        this.searchResultPoforis = resultPoforisDatas;
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
