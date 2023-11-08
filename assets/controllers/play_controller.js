import { Controller } from '@hotwired/stimulus';
import { Droppable } from '@shopify/draggable';

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
          document.location.href = document.getElementById('nb-players').getAttribute('data-start-url'); 
        }

    }

    document.addEventListener('DOMContentLoaded', function (){
      const droppable = new Droppable(
          document.querySelectorAll('.container'),
          {
              draggable: '.item',
              dropzone: '.dropzone',
          },
      );

      droppable.on('droppable:dropped', () => console.log('droppable:dropped'));
      droppable.on('droppable:returned', () => console.log('droppable:returned'));
  });

      
   }

  disconnect() {
    this.es.close();
    // disconnectStreamSource(this.es);
  }
}