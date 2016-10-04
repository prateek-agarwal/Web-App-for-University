import { Platform, MenuController, Nav } from 'ionic-angular';
import { HomePage } from '../pages/home/home';
export declare class MyApp {
    platform: Platform;
    menu: MenuController;
    nav: Nav;
    rootPage: typeof HomePage;
    pages: Array<{
        title: string;
        component: any;
    }>;
    constructor(platform: Platform, menu: MenuController);
    initializeApp(): void;
    openPage(page: any): void;
}
