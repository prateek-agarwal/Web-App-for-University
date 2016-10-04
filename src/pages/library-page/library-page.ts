import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';

/*
  Generated class for the LibraryPage page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-library-page',
  templateUrl: 'library-page.html'
})
export class LibraryPage {

  constructor(public navCtrl: NavController) {}

  ionViewDidLoad() {
    console.log('Hello LibraryPage Page');
  }

}
