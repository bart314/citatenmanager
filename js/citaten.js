const API_URL = 'api/index.php'
var navigator_items = {}
var new_quotes_file

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
    console.log(`${API_URL}/citaat/search/${searchterm}`.replaceAll(' ', '%20'))
    fetch(`${API_URL}/citaat/search/${searchterm}`.replaceAll(' ', '%20%'))
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
    let coll_id = Number(evt.currentTarget.dataset.ref)
    let quotes = [Number(evt.dataTransfer.getData('citaat_id'))]
    let body = JSON.stringify({coll_id, quotes})
    this.el = evt.currentTarget
    fetch(`${API_URL}/collections/add`, {
        method:'POST',
        body
    })
    .then ( resp => resp.json() )
    .then ( json => this.el.querySelector('.pill').innerHTML=json.tot )
}

function get_navigation_items(what) {
    fetch (`${API_URL}/${what}/all`)
    .then ( resp=> resp.json() )
    .then ( json => {
        navigator_items = json
        fill_navigator(json) 
    })
}

function drop_file(evt) {
    evt.preventDefault()
    evt.currentTarget.style.backgroundColor='white'
    const newfile = evt.dataTransfer.files[0]
    let html = '<tr><td><b>Citaten</b></td>'
    html += `<td>${newfile.name} (${newfile.size} bytes)</td></tr>`
    evt.currentTarget.querySelector('table').innerHTML += html
    new_quotes_file = newfile
}

function drag_file(evt) { 
    evt.preventDefault() 
    evt.currentTarget.style.backgroundColor='gray'
}

// Start off with all the titles in de navigation bar
get_navigation_items('titel')

document.querySelector('#zoekbalk').addEventListener('blur', zoek_citaten)
document.querySelector('#zoekknop').addEventListener('click', zoek_citaten)

/* NIEUWE TITELS OF COLLECTIONS */
document.querySelector('#new-div-container').addEventListener('click', evt => evt.currentTarget.style.display='none' )
document.querySelectorAll('#new-div-container div').forEach( el => el.addEventListener('click', evt => evt.stopPropagation()) )

document.querySelector("#btn-collecties").addEventListener('click', el => {
    document.querySelector("#titels-container h2").innerHTML = 'Collecties'
    document.querySelector('#btn-nieuwe-titel').style.display='none'
    document.querySelector('#btn-nieuwe-collectie').style.display='block'
    document.querySelector("#btn-titels").style.display='block'
    document.querySelector("#btn-collecties").style.display='none'
    get_navigation_items('collections')
})

document.querySelector("#btn-titels").addEventListener('click', (el) => {
    document.querySelector("#titels-container h2").innerHTML = 'Titels'

    el.target.style.display = 'none'
    document.querySelector("#btn-collecties").style.display='block'
    document.querySelector('#btn-nieuwe-titel').style.display='block'
    document.querySelector('#btn-nieuwe-collectie').style.display='none'
    get_navigation_items('titel')
})

document.querySelector('#filter').addEventListener('input', evt => {
    let val = evt.currentTarget.value.toLowerCase()
    let tmp = {
        type: navigator_items.type,
        data: navigator_items.data.filter( el =>  el.titel.toLowerCase().includes(val) || el.auteur?.toLowerCase().includes(val) )
    }
    fill_navigator(tmp)
})

document.querySelector('#btn-nieuwe-collectie').addEventListener('click', evt => {
    document.getElementById('new-div-container').style.display = 'flex'
    document.getElementById('new-collection-div').style.display = 'block'
    document.getElementById('new-title-div').style.display = 'none'
})

document.querySelector('#btn-nieuwe-titel').addEventListener('click', evt=> {
    console.log(['nieuwe titel'])
    console.log(evt)
    console.log(evt.dataTransfer)
    fetch(`${API_URL}/auteur/all`)
    .then( resp => resp.json() )
    .then( json => {
        const select = document.querySelector('#new-title-div select')
        select.innerHTML = '<option>===selecteer===</option>'
        json.forEach( el => {
            let option = document.createElement('option')
            option.setAttribute('value', el.id)
            option.innerHTML = `${el.voornaam} ${el.achternaam}`
            select.appendChild(option)
        })
        document.getElementById('new-div-container').style.display = 'flex'
        document.getElementById('new-collection-div').style.display = 'none'
        document.getElementById('new-title-div').style.display = 'block'

    })
})

document.querySelectorAll('button.btn-save').forEach( el => el.addEventListener('click', evt => {
    evt.preventDefault()
    const form = evt.target.form
    let body = new FormData(form)
    body.append('quotes', new_quotes_file)

    if (body.voornaam !='' || body.achternaam !='') delete body.auteur_id 

    const options = {
        method:'POST',
        body
    }

    fetch(`${API_URL}${form.dataset.action}`, options)
    .then( resp => resp.json() )
    .then( json => {
        console.log(json)
        document.getElementById('new-div-container').style.display='none'
        document.getElementById('feedback').innerHTML = `${json.aantal_quotes} citaten toegevoegd.`
        document.getElementById('feedback').style.display = 'block';
        setTimeout (() => document.getElementById('feedback').style.display='none', 5000)
    })
}))


