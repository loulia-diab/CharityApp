import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donaition_category_view_page.dart';
import 'package:charity_project/view/donate_campaigns_page.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';

class OneHumanitariancasesViewPage extends StatefulWidget {
  const OneHumanitariancasesViewPage({super.key});

  @override
  State<OneHumanitariancasesViewPage> createState() => _OneHumanitariancasesViewPageState();
}

class _OneHumanitariancasesViewPageState extends State<OneHumanitariancasesViewPage> {
  @override
  Widget build(BuildContext context) {
   final progress = (raisedamount / goalamount).clamp(0.0, 1.0);
    return Scaffold(
      appBar: AppBar(
        backgroundColor: AppColors.background,
      ),
      body: BackgroundWrapper(child: 
      SizedBox(height: 800,
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.only(right: 20,left: 20),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(8.0),
                child: Container(
                  height: 200,width: double.infinity,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(20),
                    image: DecorationImage(image: AssetImage("assets/images/case.jpg"),fit:BoxFit.cover )
                  ),
                ),
            
              ),
              Text('Emergency Surgery for a Child',style: AppTextStyle.a,),
              Divider(),
              SizedBox(height: 10,),
              Text('About Campaign ',style: AppTextStyle.helpReq,),
              SizedBox(height: 10,),
              Text("Ahmed, a 6-year-old boy, suffers from a congenital heart defect that requires immediate surgical intervention.\n  His family cannot afford the high cost of the operation, and without it, his life is at serious risk.\n  Your support can make the difference between life and death.\nEvery contribution helps cover the surgery expenses, post-operative care, and medications.\nLet’s stand together to give Ahmed a second chance at life.",
              style: AppTextStyle.d,)


  
  
              ,SizedBox(height: 20,),
               Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Padding(
                                    padding: const EdgeInsets.only(left: 10,),
                                    child: Row(
                                      children: [
                                     Text("\$${raisedamount.toInt()} ",style: AppTextStyle.a,),
                                      Text("of \$${goalamount.toInt()} ",style: AppTextStyle.agray,),
                                      SizedBox(width: 120,),
                                      Text("90% ",style: AppTextStyle.a,),
                                    ],),
                                  ),
                                  Padding(
                                    padding: const EdgeInsets.all(8.0),
                                    child: SizedBox(width: 800,
                                      child: LinearPercentIndicator(
                                        maskFilter: MaskFilter.blur(BlurStyle.solid, 3),
                                        linearGradient: LinearGradient(colors: [AppColors.primary,AppColors.teal]),
                                        barRadius: Radius.circular(10),
                                        curve: Curves.easeInOut,
                                        clipLinearGradient: true,
                                        lineHeight: 15,
                                        // progressColor: AppColors.primary,
                                        percent: progress,
                                        animation: true,
                                        animationDuration: 1000,
                                        
                                      ),
                                    ),
                                  ),
                                  Padding(
                                    padding: const EdgeInsets.only(left: 200),
                                    child: Text("\$ 200 Remaining",style: AppTextStyle.d,),
                                  ),
                                  
                                  
                                ],
                              ),
                            

                              Padding(
               padding:  EdgeInsets.only(right: 20,top: 30,bottom: 20),
               child: Center(
                 child: ElevatedButton(onPressed: (){
                  
                  Navigator.push(context, MaterialPageRoute(builder: (context)=> DonateCampaignsPage()));
                 }, child: Text('Donate'),
                 style: ElevatedButton.styleFrom(
                   backgroundColor: AppColors.secondary,
                   fixedSize: Size(200, 50),
                   foregroundColor: AppColors.white
                 ),
                 ),
               ),
             )
                              
            ],
            ),
          ),
        ),
      )),
    );
  }
}