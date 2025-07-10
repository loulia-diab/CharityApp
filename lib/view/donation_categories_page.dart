import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/Kaffarat_and_Sadaqah_view.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/before_inKind_donaition.dart';
import 'package:charity_project/view/campaigns_page.dart';
import 'package:charity_project/view/general_donaition_page.dart';
import 'package:charity_project/view/homa_page.dart';
import 'package:flutter/material.dart';

class DonationCategoriesPage extends StatelessWidget {
   DonationCategoriesPage({super.key});

List<Map<String,String>> DonationCategories = [

{'title':'Campaigns',
'image':"assets/images/camp.png"},

{"title":'HumanitarianCases',
"image":"assets/images/aa.png"},


{'title':'Kaffarat and Sadaqah',
"image":"assets/images/kk.png"},


{'title':'Sponsorships',
"image":"assets/images/mmmm.png"},

{'title':'General Donation',
"image":"assets/images/iii.png"},

{'title':'In-kind Donations',
"image":"assets/images/l.png"}



]
;




  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: BackgroundWrapper(
          child: Column(
        children: [
          AppBar(
            backgroundColor: AppColors.white,
            // elevation: 5,
            // shadowColor: AppColors.unselected,
            title: Text(
              'Donaition Categories',
              style: TextStyle(
                  color: AppColors.primary, fontWeight: FontWeight.w700),
            ),
          ),
          SizedBox(height: 50,),
          SizedBox(height: 560,
            child: Padding(
              padding: const EdgeInsets.all(8.0),
              child: GridView.builder(
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2, crossAxisSpacing: 5, mainAxisSpacing: 10,childAspectRatio:1.1),
                      itemCount: 6,
                  itemBuilder: (context,index){
              return InkWell( onTap: (){
       if (DonationCategories[index]["title"] == "Campaigns" ||
      DonationCategories[index]["title"] == "HumanitarianCases" || 
      DonationCategories[index]["title"] == "Sponsorships") {
                     Navigator.push(context, MaterialPageRoute(builder: (context)=> CampaignsPage(category: DonationCategories[index]["title"]!,)));
                }
             else if (DonationCategories[index]["title"]== "Kaffarat and Sadaqah"){
        Navigator.push(context, MaterialPageRoute(builder: (context)=> KaffaratAndSadaqahView()));
             }
             else if (DonationCategories[index]["title"]== "General Donation"){
              //  Navigator.push(context, MaterialPageRoute(builder: (context)=> GeneralDonaitionPage()));
             }
             else if (DonationCategories[index]["title"]== "In-kind Donations"){
               Navigator.push(context, MaterialPageRoute(builder: (context)=> BeforeInkindDonaition()));
             }
             
              },
                child: Card(
                  color: const Color.fromARGB(255, 248, 247, 245),
                          elevation: 20,
                          shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12)
                          ),
                          child: Container(
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(12),
                              // image: DecorationImage(image: AssetImage('assets/images/zzz.png'),fit:BoxFit.cover )
                            ),
                            child: Column(
                              children: [
                                Stack(
                                  children:[
                                    Padding(
                                      padding: const EdgeInsets.only(top: 10),
                                      child: Image.asset('assets/images/111.png',height: 120,width: 120,),
                                    ),
                                    Positioned(top: 25,left: 20,
                                      child: Image.asset(height: 85,width: 85,
                                      DonationCategories[index]["image"]!,color: AppColors.primary,
                                                                        
                                                                        ),
                                    ),
                                  ] 
                                ),
                                Text(DonationCategories[index]['title']!,style: TextStyle(
                                  color: AppColors.primary,fontWeight: FontWeight.w600
                                ),)
                              ],
                            ),
                          ),
                ),
              );
                  }),
            ),
          )
        ],
      )),
    );
  }
}
