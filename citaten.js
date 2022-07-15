const API_URL = 'http://localhost:8080/server.php'
var navigator_items = {}

function titel_citaten(evt) {
    document.querySelectorAll('div.titel').forEach ( tit => tit.classList.remove('active') )
    id = evt.currentTarget.dataset.ref
    evt.currentTarget.classList.add('active')
    console.log(evt.currentTarget.dataset.type)

    const endpoint = evt.currentTarget.dataset.type=='collection' ? 'collections' : 'titel'

    fetch(`${API_URL}/${endpoint}/${id}/all`)
    .then( resp => resp.json() )
    .then( json => show_citaten(json) )
}

function show_citaten(json) {
    const div = document.querySelector("#citaten")
    div.innerHTML = ''
    json.forEach ( el => {
        const templ = document.querySelector("#citaat-template").content.cloneNode(true)
        const maindiv = templ.querySelector('div')
        templ.querySelector(".content").innerHTML = el.citaat
        templ.querySelector(".ref").innerHTML = el.pagina
        maindiv.setAttribute('data-citaat_id', el.id)
        //maindiv.addEventListener('drag', evt => evt.dataTransfer.setData('citaat_id', el.id) )
        maindiv.addEventListener('click', get_citaat)
        div.append(templ)
})
}

function zoek_citaten(el) {
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

function fill_navigator(json) {
    const div = document.querySelector("#titels")

    div.innerHTML = ''
    json.data?.forEach ( el => {
        const templ = document.querySelector("#titel-template").content.cloneNode(true)
        const maindiv = templ.querySelector('div')
        let aut = el.auteur ? el.auteur : '&nbsp;'
        templ.querySelector('h1').innerHTML = el.titel
        templ.querySelector('p').innerHTML = `${aut} <span class="pill">${el.aantal}</span>`
        maindiv.setAttribute('data-ref', el.id)
        if (json.type=='collection') {
            maindiv.addEventListener('dragover', evt => evt.preventDefault() )
            maindiv.addEventListener('drop', evt => citaat_to_collection(evt) )
            maindiv.setAttribute('data-type','collection')
        } else {
            maindiv.setAttribute('data-type','titel')
        }
        maindiv.addEventListener('click', titel_citaten)

        div.appendChild(templ)
    })
}

function get_citaat(id) {
    console.log(id) // TODO
}

function drag(evt) {
    console.log(evt.currentTarget)
    evt.currentTarget.classList.add('dragging')
    evt.dataTransfer.setData('citaat_id', evt.target.dataset.citaat_id)
}

function citaat_to_collection(evt) {
    evt.preventDefault()
    let coll_id = evt.currentTarget.dataset.ref
    let quotes = [evt.dataTransfer.getData('citaat_id')]
    let body = JSON.stringify({coll_id, quotes})
    this.el = evt.currentTarget
    fetch(`${API_URL}/collections/add`, {
        method:'POST',
        headers: {'content-type':'application.json'},
        body
    })
    .then ( resp => resp.json() )
    .then ( json => this.el.querySelector('.pill').innerHTML=json.tot )


}

// Start off with all the titles in de navigation bar
fetch (`${API_URL}/titel/all`)
.then ( resp=> resp.json() )
.then ( json => {
    navigator_items = json
    fill_navigator(json) 
})

document.querySelector('#zoekbalk').addEventListener('blur', zoek_citaten)
document.querySelector('#zoekknop').addEventListener('click', zoek_citaten)

document.querySelector("#btn-collecties").addEventListener('click', (el) => {
    document.querySelector("#titels-container h2").innerHTML = 'Collecties'
    document.querySelector('#btn-nieuwe-titel').style.display='none'
    document.querySelector('#btn-nieuwe-collectie').style.display='block'
    document.querySelector("#btn-titels").style.display='block'
    document.querySelector("#btn-collecties").style.display='none'
    fetch(`${API_URL}/collections/all`)
    .then( resp => resp.json() )
    .then( json => {
        navigator_items = json
        fill_navigator(json) 
    })
})

document.querySelector("#btn-titels").addEventListener('click', (el) => {
    document.querySelector("#titels-container h2").innerHTML = 'Titels'

    el.target.style.display = 'none'
    document.querySelector("#btn-collecties").style.display='block'
    document.querySelector('#btn-nieuwe-titel').style.display='block'
    document.querySelector('#btn-nieuwe-collectie').style.display='none'
    fetch(`${API_URL}/titel/all`)
    .then( resp => resp.json() )
    .then( json => {
        navigator_items = json
        fill_navigator(json) 
    })

})

document.querySelector('#filter').addEventListener('input', evt => {
    let val = evt.currentTarget.value.toLowerCase()
    let tmp = {
        type: navigator_items.type,
        data: navigator_items.data.filter( el =>  el.titel.toLowerCase().includes(val) || el.auteur?.toLowerCase().includes(val) )
    }
    fill_navigator(tmp)
})

document.querySelector('#btn-nieuwe-titel').addEventListener('click', evt=> {
    console.log(['nieuwe titel'])
})


