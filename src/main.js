import './style.scss'
import javascriptLogo from './javascript.svg'
import viteLogo from '/vite.svg'
import { setupCounter } from './counter.js'
import '/vendor/fullcalendar/main.min.js';

document.querySelector('#app').innerHTML = `
  <div>
    <a href="https://vite.dev" target="_blank">
      <img src="${viteLogo}" class="logo" alt="Vite logo" />
    </a>
    <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank">
      <img src="${javascriptLogo}" class="logo vanilla" alt="JavaScript logo" />
    </a>
    <h1>Hello Vite!</h1>
    <div class="card">
      <button id="counter" type="button"></button>
    </div>
    <p class="read-the-docs">
      Click on the Vite logo to learn more
    </p>
  </div>
`

setupCounter(document.querySelector('#counter'))

// Aquí es donde agregamos el código de fetch
fetch('http://localhost/api/citas.php?action=list')
  .then(response => response.json())
  .then(data => {
    console.log(data);  // Muestra las citas en la consola
    // Aquí puedes agregar lógica para mostrar los datos en la interfaz de usuario si lo necesitas.
  })
  .catch(error => console.error('Error:', error));
