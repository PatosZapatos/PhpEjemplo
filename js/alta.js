const form = document.querySelector(".altaInput");

form.addEventListener("submit", alta);

function alta(event) {
  event.preventDefault();

  let p = document.querySelector("p");

  axios
    .post("https://jsonplaceholder.typicode.com/posts", {
      data: {
        userId: 1,
        title: "Esto es un post nuevo",
        body: "cuerpo de este post. Me gusta la librería Axios!!",
      },
    })
    .then(function (res) {
      if (res.status == 201) {
        p.innerHTML = "El nuevo Post ha sido almacenado con id: " + res.data.id;
      }
    })
    .catch(function (err) {
      console.log(err);
    });
}
