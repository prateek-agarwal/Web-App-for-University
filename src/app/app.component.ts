import { Component, ViewChild} from '@angular/core';
import { Platform, MenuController, Nav } from 'ionic-angular';
import { StatusBar } from 'ionic-native';

import { HomePage } from '../pages/home/home';
import { GatepassPage } from '../pages/gatepass-page/gatepass-page';
import { TimetablePage } from '../pages/timetable-page/timetable-page';
import { LibraryPage } from '../pages/library-page/library-page';


@Component({
  templateUrl: 'app.html'
})
export class MyApp {
  @ViewChild(Nav) nav: Nav;

  rootPage = HomePage;
  pages: Array<{title: string, component: any}>;

  constructor(public platform: Platform,
    public menu: MenuController) {

    this.initializeApp();
    this.pages = [
      { title: 'Home', component: HomePage },
      { title: 'Gatepass', component: GatepassPage },
      { title: 'Timetable', component: TimetablePage},
      { title: 'Library', component: LibraryPage}
    ];
  }
  initializeApp() {
    this.platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      StatusBar.styleDefault();
    });
  }
  openPage(page) {
    // close the menu when clicking a link from the menu
    this.menu.close();
    // navigate to the new page if it is not the current page
    this.nav.setRoot(page.component);
  }

}
