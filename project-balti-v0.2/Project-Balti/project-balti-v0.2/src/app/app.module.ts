import { NgModule } from '@angular/core';
import { IonicApp, IonicModule } from 'ionic-angular';
import { MyApp } from './app.component';
import { HomePage } from '../pages/home/home';
import { Timetable } from '../pages/timetable/timetable';
import { UserService } from '../providers/user-service';
import { Login } from '../pages/login/login';
import { GatepassService } from '../providers/gatepass-service';
import { Login2 } from '../pages/login2/login2';
import { GatepassHome } from '../pages/gatepass-home/gatepass-home';
import { GatepassStep1 } from '../pages/gatepass-step1/gatepass-step1';
import { LocalGatepass } from '../pages/local-gatepass/local-gatepass';
import { OutstationGatepass } from '../pages/outstation-gatepass/outstation-gatepass';
import { GatepassFinal } from '../pages/gatepass-final/gatepass-final';
import { VariableTiming } from '../pages/variable-timing/variable-timing';
import { TimetableDetails } from '../pages/timetable-details/timetable-details';
import { FixedTiming } from '../pages/fixed-timing/fixed-timing';
import { Attendance } from '../pages/attendance/attendance';
import { LibraryHome } from '../pages/library-home/library-home';
import { LibrarySearch } from '../pages/library-search/library-search';
import { LibraryService } from '../providers/library-service';

@NgModule({
  declarations: [
    MyApp,
    HomePage,
    Timetable,
    Login,
    Login2,
    GatepassHome,
    GatepassStep1,
    LocalGatepass,
    OutstationGatepass,
    GatepassFinal,
    VariableTiming,
    TimetableDetails,
    FixedTiming,
    Attendance,
    LibraryHome,
    LibrarySearch
  ],
  imports: [
    IonicModule.forRoot(MyApp)
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    HomePage,
    Timetable,
    Login,
    Login2,
    GatepassHome,
    GatepassStep1,
    LocalGatepass,
    OutstationGatepass,
    GatepassFinal,
    VariableTiming,
    TimetableDetails,
    FixedTiming,
    Attendance,
    LibraryHome,
    LibrarySearch
  ],
  providers: [UserService, GatepassService, LibraryService]
})
export class AppModule {}
