import { Component }      from '@angular/core';
import { Auth, User }     from '@ionic/cloud-angular';
import { NavController }  from 'ionic-angular';



@Component({
  templateUrl: 'build/pages/home/home.html'
})
export class HomePage {
  constructor(public auth: Auth, public user: User, public navCtrl: NavController) {

  }
}
