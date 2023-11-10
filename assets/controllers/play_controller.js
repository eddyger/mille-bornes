import { Controller } from '@hotwired/stimulus';
import { Droppable } from '@shopify/draggable';
import * as Turbo from '@hotwired/turbo';

// import { connectStreamSource, disconnectStreamSource } from "@hotwired/turbo";

export default class extends Controller {
  static values = { url: String };

  connect() {
    this.es = new EventSource(this.urlValue);
    // connectStreamSource(this.es);
    this.es.onmessage = event => {
        let message = JSON.parse(event.data);
        console.log(message);
        if (message.event === 'NewUserHasJoinedEvent'){
            let nbPlayers = message.nbPlayers;
            document.getElementById('nb-players').innerText = nbPlayers;
            let buttonStart = document.getElementById('button-start-game');
            if (nbPlayers >= 2 && buttonStart ){
              buttonStart.disabled = false;
            }
        }

        if (message.event === 'GameIsStartedEvent'){
          Turbo.visit(document.getElementById('nb-players').getAttribute('data-start-url')); 
        }

        if (message.event === 'PlayerHavePlayedEvent' ){
          Turbo.visit(window.location);
        }

    }
    var _self = this;
    let pageOnloadHandler = function (){
      if (_self.alreadyLoad){
        return;
      }
      _self.alreadyLoad = true;
      
      const droppable = new Droppable(
          document.querySelectorAll('.container'),
          {
              draggable: '.item',
              dropzone: '.dropzone',
          },
      );

      droppable.on('droppable:dropped', (event) => {
        console.log('droppable:dropped')
        console.log(event);
      });
      droppable.on('droppable:returned', (event) => console.log('droppable:returned'));
      droppable.on("drag:stop", event => { 
        console.log('drag:stop')
        console.log(event.source.parentNode);
      });

      // Handle play button
      const playButton = document.getElementById('button-play-cards');
      if (playButton){
        playButton.addEventListener('click', (event) => {
          event.preventDefault();
          _self.playCards(event.target.href);
        });
      }
      
    };

    document.addEventListener('DOMContentLoaded', pageOnloadHandler);
    document.addEventListener('turbo:load', pageOnloadHandler);

   }

  async playCards(url)
    /* Json body : {
        'trash' : 'cardCode',  
        'table': 'cardCode'
        'opponent' : {'card':'cardCode', 'player':'playerId'}
      }
    */{
    const trashElement = document.getElementById('trash');
    let items = trashElement.getElementsByClassName('item');  
    const trashCardCode = items.length > 0 ? items[0].dataset.code : ''; 
    
    const tableElement = document.getElementById('table');
    items = tableElement.getElementsByClassName('item');  
    const tableCardCode = items.length > 0 ? items[0].dataset.code : ''; 
    

    let postBody = {
      trash : trashCardCode,
      table : tableCardCode
    }
    
    const response = await fetch(url,{
      method: "POST",
      body : JSON.stringify(postBody)
    });

    const text = await response.text();
    Turbo.renderStreamMessage(text);
      
    console.log(url);
  }

  disconnect() {
    this.es.close();
    // disconnectStreamSource(this.es);
  }
}