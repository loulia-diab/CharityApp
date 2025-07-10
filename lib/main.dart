import 'package:charity_project/view/charity_fund_page.dart';
import 'package:charity_project/view/homa_page.dart';
import 'package:charity_project/view/main_navBar_page.dart';
import 'package:charity_project/view/nav.dart';
import 'package:charity_project/view/request_help_page.dart';
import 'package:flutter/material.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: MainNavbarPage(),
    );
  }
}

