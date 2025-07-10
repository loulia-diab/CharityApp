import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donate_page.dart';
import 'package:charity_project/view/input_decoraition.dart';
import 'package:flutter/material.dart';

class GeneralDonaitionPage extends StatefulWidget {
  const GeneralDonaitionPage({super.key});

  @override
  State<GeneralDonaitionPage> createState() => _GeneralDonaitionPageState();
}

class _GeneralDonaitionPageState extends State<GeneralDonaitionPage> {
  List<String> generaldonaitiotype = [
    "General Donation",
    "Support the Team",
    "Support the Humanitarian Section",
    "Support the Orphans Section",
    "Support the Medical Section"
  ];
  String? selectedtype = "General Donation" ;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.white,
        title: Text("General Donaition",style: AppTextStyle.a,),
      ),
      body: BackgroundWrapper(child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20,vertical: 20),
              child: DropdownButtonFormField(
                decoration: AppInputDecoration.defaultDecoration,
                value: selectedtype,
                items: generaldonaitiotype.map((item)=> DropdownMenuItem<String>(value: item,
                child: Text(item))).toList(), onChanged: (value){
                  setState(() {
                    selectedtype = value;
              
                  });
                }),
            ),
          ),
          Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20,vertical: 20),
                child: Text("Set Donation Amount",style: AppTextStyle.a,),
              ),
              
          DonateWidget()
        ],
      )),
    );
  }
}