import { Component, ViewChild } from '@angular/core';
import { NavController } from 'ionic-angular';
import { Gatepass } from '../../classes/gatepass';
import { Login } from '../login/login';
import { UserService } from '../../providers/user-service';
import { LibraryService } from '../../providers/library-service';

/*
  Generated class for the LibrarySearch page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-library-search',
  templateUrl: 'library-search.html'
})
export class LibrarySearch {

    public keyword : any;
    public library : any;

  constructor(public navCtrl: NavController,
    public libraryService: LibraryService) {

    }

  ionViewDidLoad() {
    console.log('Hello LibrarySearch Page');

    this.libraryService.getBook(this.keyword)
    .subscribe(
      data => {
        this.library = data;
        console.log("Library Search_object: ",JSON.stringify(this.library));
      }
    );
  }
}