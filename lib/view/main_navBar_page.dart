import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/before_help_request.dart';
import 'package:charity_project/view/charity_fund_page.dart';
import 'package:charity_project/view/donation_categories_page.dart';
import 'package:charity_project/view/homa_page.dart';
import 'package:charity_project/view/my_list_page.dart';
import 'package:charity_project/view/request_help_page.dart';
import 'package:flutter/material.dart';

class MainNavbarPage extends StatefulWidget {
  const MainNavbarPage({super.key});

  @override
  State<MainNavbarPage> createState() => _MainNavbarPageState();
}

class _MainNavbarPageState extends State<MainNavbarPage> {
  int selectedIndex = 0;
  List<Widget> pages = [
    HomaPage(),
    DonationCategoriesPage(),
    CharityFundPage(),
    BeforeHelpRequest(),
    MyListPage()
  ];
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      bottomNavigationBar: NavigationBar(
        
        backgroundColor: AppColors.white,
        animationDuration: Duration(seconds: 4),
        shadowColor: Colors.grey,
        selectedIndex: selectedIndex,
        labelBehavior:NavigationDestinationLabelBehavior.alwaysHide,
        indicatorColor: Colors.transparent,
        onDestinationSelected: (index){
          setState(() {
            selectedIndex=index;
          });
        },
        
        
        
        
        
        
        
        destinations: [
        NavigationDestination(
            icon: Icon(
              Icons.home_outlined,
              color: AppColors.secondary,
            ),
            selectedIcon: Icon(
              Icons.home_outlined,
              color: AppColors.primary,
            ),
            label: 'Home'),
        NavigationDestination(icon: Icon(Icons.category,color:AppColors.secondary,), label: 'donaition categories',selectedIcon: Icon(Icons.category,color: AppColors.primary,),),
  NavigationDestination(icon: Image.asset('assets/images/fund.png',height: 30,width: 30,color:AppColors.secondary ,),  selectedIcon:  Image.asset('assets/images/fund.png',height: 30,width: 30,color:AppColors.primary )
 , label: 'charity fund'),
       NavigationDestination(icon: Image.asset('assets/images/help.png',height: 40,width: 40,color:AppColors.secondary ),selectedIcon: 
       Image.asset('assets/images/help.png',height: 40,width: 40,color:AppColors.primary ), label: 'help request'),
       NavigationDestination(icon: Icon(Icons.format_list_bulleted,color: AppColors.secondary,), selectedIcon: Icon(
              Icons.format_list_bulleted,
              color: AppColors.primary,
            ), label: 'my list')
      ]),
body: pages[selectedIndex],


       
      
    );
  }
}
