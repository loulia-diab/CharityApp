import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donaition_category_view_page.dart';
import 'package:charity_project/view/one_HumanitarianCases_view_page.dart';
import 'package:charity_project/view/one_campaign_page.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';

class HumanitariancasesViewPage extends StatefulWidget {
  const HumanitariancasesViewPage({super.key});

  @override
  State<HumanitariancasesViewPage> createState() => _HumanitariancasesViewPageState();
}

class _HumanitariancasesViewPageState extends State<HumanitariancasesViewPage> {
  @override
  Widget build(BuildContext context) {
      return Scaffold(
        backgroundColor: AppColors.background,
        body: BackgroundWrapper(
        child: Expanded(child: ListView.builder(itemCount: 10,
          itemBuilder: (context,index){
            return Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10,vertical: 5),
              child: Container(
                height: 190,
                width: double.infinity,
                child: Card(
                  elevation: 10,
                  color: AppColors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)
                  ),
                  child: Row(
                    children: [
                      Padding(
                        padding: const EdgeInsets.only(left: 20,right: 20),
                        child: Container(
                          height: 100,
                          width: 100,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(10),
                            image: DecorationImage(image: AssetImage("assets/images/case.jpg",),fit: BoxFit.cover)
                          ),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.only(top:20),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("Emergency Surgery for a Child",style: AppTextStyle.b,),
                            SizedBox(height: 50,width: 200,
                              child: Text("A 6-year-old child urgently needs life-....",style: AppTextStyle.c,)),
                            SizedBox(height: 10,),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                
                                SizedBox(width: 200,
                                  child: LinearPercentIndicator(
                                    maskFilter: MaskFilter.blur(BlurStyle.solid, 3),
                                    linearGradient: LinearGradient(colors: [AppColors.primary,AppColors.teal]),
                                    barRadius: Radius.circular(10),
                                    curve: Curves.easeInOut,
                                    clipLinearGradient: true,
                                    lineHeight: 10,
                                    // progressColor: AppColors.primary,
                                    percent: progress,
                                    animation: true,
                                    animationDuration: 1000,
                                    
                                  ),
                                ),
                                Padding(
                                  padding: const EdgeInsets.only(left: 10),
                                  child: Row(
                                    children: [
                                   Text("\$${raisedamount.toInt()} / \$${goalamount.toInt()}", style: AppTextStyle.c)
                                  
                                  ],),
                                ),
                              ],
                            ),
                            Padding(
                              padding: EdgeInsets.only(left: 90,top: 5),
                              child: ElevatedButton(onPressed: (){
                                Navigator.push(context, MaterialPageRoute(builder: (context)=>OneHumanitariancasesViewPage(
        
                                )));
                              }, child: Text("Details"),
                              style:ElevatedButton.styleFrom(
                                backgroundColor: AppColors.secondary,
                                foregroundColor: AppColors.white,
                                fixedSize: Size(100, 30)
                              ) ,),
                            )
                          ],
                          
                        ),
                      )
                    ],
                  ),
                ),
              ),
            );
          }
          )
          
          ),
            ),
      );
  }
}