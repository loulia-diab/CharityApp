import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donaition_category_view_page.dart';
import 'package:charity_project/view/donate_campaigns_page.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';

class OneCampaignPage extends StatefulWidget {
  const OneCampaignPage({super.key});

  @override
  State<OneCampaignPage> createState() => _OneCampaignPageState();
}

class _OneCampaignPageState extends State<OneCampaignPage> {
  @override
  Widget build(BuildContext context) {
    final progress = (raisedamount / goalamount).clamp(0.0, 1.0);
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.white,
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
                    image: DecorationImage(image: AssetImage("assets/images/d.jpg"),fit:BoxFit.cover )
                  ),
                ),
            
              ),
              Text('Clean Water Project',style: AppTextStyle.a,),
              Divider(),
              SizedBox(height: 10,),
              Text('About Campaign ',style: AppTextStyle.helpReq,),
              SizedBox(height: 10,),
              Text("Clean Water Project in Sudan aims to provide safe drinking water\nto underserved communities through well drilling and filtration systems.\nWith your support, we bring better health and hope to children and families in need.",style: AppTextStyle.d,),
              SizedBox(height: 20,),
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
                              Divider(),
                              Text("More details :",style: AppTextStyle.helpReq,) ,
                              Row(mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                                children: [
                                  Padding(
                                    padding: const EdgeInsets.only(top: 20),
                                    child: Column(
                                      children: [
                                        Icon(Icons.people,color: AppColors.primary,size: 30,),
                                         Text("Number of\nBeneficiaries",style: TextStyle(
                                           
    color: Color.fromARGB(148, 0, 0, 0),
    fontWeight: FontWeight.w600,
    fontSize: 14,

  
                                         ),textAlign: TextAlign.center,),
                                        SizedBox(height: 5,),
                                        Text("360 person",style: AppTextStyle.helpReq,)
                                      ],
                                    ),
                                  ),
                                  SizedBox(height: 100,
                                    child: VerticalDivider()),
                                Padding(
                                    padding: const EdgeInsets.only(top: 25),
                                    child: Column(
                                      children: [
                                        Icon(Icons.query_builder,color: AppColors.primary,size: 30,),
                                        SizedBox(height: 10,),
                                         Text("Start Date",style: AppTextStyle.d,textAlign: TextAlign.center,),
                                        SizedBox(height: 10,),
                                        Text("20/1/2024",style: AppTextStyle.helpReq,)
                                      ],
                                    ),
                                  ),
                                  SizedBox(height: 100,
                                    child: VerticalDivider()),
                                  Padding(
                                    padding: const EdgeInsets.only(top: 25),
                                    child: Column(
                                      children: [
                                        Icon(Icons.history,color: AppColors.primary,size: 32,),
                                        SizedBox(height: 10,),
                                         Text("End Date",style: AppTextStyle.d,textAlign: TextAlign.center,),
                                        SizedBox(height: 10,),
                                        Text("20/1/2025",style: AppTextStyle.helpReq,)
                                      ],
                                    ),
                                  )
          
          
          
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