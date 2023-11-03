import { Controller } from '@hotwired/stimulus';
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
            if (nbPlayers >= 2){
                document.getElementById('button-start-game').disabled = false;
            }
        }  
    }
      
   }

  disconnect() {
    this.es.close();
    // disconnectStreamSource(this.es);
  }
}