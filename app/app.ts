import { Component } from '@angular/core';
import { ionicBootstrap, Platform } from 'ionic-angular';
import { StatusBar } from 'ionic-native';
import {provideCloud, CloudSettings} from '@ionic/cloud-angular';

import { HomePage } from './pages/home/home';

const cloudSettings: CloudSettings = {
  'core': {
    'app_id': 'fc7c3dff'
  }
};

@Component({
  template: '<ion-nav [root]="rootPage"></ion-nav>'
})
export class MyApp {
  rootPage: any = HomePage;

  constructor(public platform: Platform) {
    platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      StatusBar.styleDefault();
    });
  }
}

ionicBootstrap(MyApp, [provideCloud(cloudSettings)]);
