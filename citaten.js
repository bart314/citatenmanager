const API_URL = 'http://localhost:8080/server.php'

const titel_citaten = (el) => {
    document.querySelectorAll('div.titel').forEach ( tit => tit.classList.remove('active') )
    try {  // chromium
        id = el.path[1].dataset.ref
        el.path[1].classList.add('active')
    } catch (error) {  // firefox
        id = el.target.parentElement.dataset.ref
        el.target.parentElement.classList.add('active')
    } 

    fetch(`${API_URL}/titel/${id}/all`)
    .then( resp => resp.json() )
    .then( json => show_citaten(json) )
}



function show_citaten(json) {
    console.log(json)
    const div = document.querySelector("#citaten")
    div.innerHTML = ''
    json.forEach ( el => {
        const templ = document.querySelector("#citaat-template").content.cloneNode(true)
        const maindiv = templ.querySelector('div')
        templ.querySelector(".content").innerHTML = el.citaat
        templ.querySelector(".ref").innerHTML = el.pagina
        maindiv.setAttribute('data-ref', el.id)
        maindiv.addEventListener('click', get_citaat)
        div.append(templ)
})
}

const get_citaat = (el) => {
    console.log(el)
}
const zoek_citaten = (el) => {
    const searchterm = document.getElementById('zoekbalk').value

    fetch(`${API_URL}/citaat/search/${searchterm}`)
    .then ( resp => resp.json() )
    .then ( json => {
        json.forEach ( el => {
            el.citaat = el.citaat.replaceAll(searchterm, `<b>${searchterm}</b>`)
            el.pagina = `${el.titel}, p.${el.pagina}`
        })
        show_citaten(json)
    })
}



// Start off with all the titles in de navigation bar
fetch (`${API_URL}/titel/all`)
.then ( resp=> resp.json() )
.then ( json => {
    const div = document.querySelector("#titels")
    json.forEach ( el => {
        const templ = document.querySelector("#titel-template").content.cloneNode(true)
        const maindiv = templ.querySelector('div')
        templ.querySelector('h1').innerHTML = el.titel
        templ.querySelector('p').innerHTML = `${el.auteur} <span class="pill">${el.aantal}</span>`
        maindiv.setAttribute('data-ref', el.id)
        maindiv.addEventListener('click', titel_citaten)
        div.appendChild(templ)
    })
})

document.querySelector('#zoekbalk').addEventListener('blur', zoek_citaten)
document.querySelector('#zoekknop').addEventListener('click', zoek_citaten)

