import { NgModule } from '@angular/core';
import { IonicApp, IonicModule } from 'ionic-angular';
import { MyApp } from './app.component';

import { HomePage } from '../pages/home/home';
import { GatepassPage } from '../pages/gatepass-page/gatepass-page';
import { TimetablePage } from '../pages/timetable-page/timetable-page';
import { LibraryPage } from '../pages/library-page/library-page';


@NgModule({
  declarations: [
    MyApp,
    HomePage,
    GatepassPage,
    TimetablePage,
    LibraryPage
  ],
  imports: [
    IonicModule.forRoot(MyApp)
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    HomePage,
    GatepassPage,
    TimetablePage,
    LibraryPage
  ],
  providers: []
})
export class AppModule {}
