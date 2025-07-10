import 'package:carousel_slider/carousel_slider.dart';
import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/before_gift.dart';
import 'package:charity_project/view/before_inKind_donaition.dart';
import 'package:charity_project/view/before_volunteer_page.dart';
import 'package:charity_project/view/d.dart';
import 'package:charity_project/view/emergency_cases_page.dart';
import 'package:charity_project/view/pay_details_page.dart';
import 'package:charity_project/view/periodically_donaition.dart';
import 'package:charity_project/view/sadakah_page.dart';
import 'package:charity_project/view/squares_page.dart';
import 'package:charity_project/view/zakah_page.dart';
import 'package:flutter/material.dart';


List<String> titles = [
  'إطعام وحياة',
  "غذاء مستخدم",
  "لبيه غزة",
  "فرقان",
  "الأقربون أولى",
  "ماء"
];






class HomaPage extends StatelessWidget {
  const HomaPage({super.key});

  @override
  Widget build(BuildContext context) {
    return 
    Scaffold(
      backgroundColor:
      AppColors.background,
      //  const Color.fromARGB(255, 246, 240, 232),
      //  const Color.fromARGB(255, 249, 247, 243),
      body: BackgroundWrapper(
        
        child: SizedBox(
          height: 700,
          child: ListView(
            
          children: [
            AppBar(
              title: Text('Kun Auna', style: TextStyle(
                  color: AppColors.primary, fontWeight: FontWeight.w700),),
              backgroundColor: AppColors.white,
              // elevation: 5,
              // shadowColor: AppColors.unselected,
      
              actions: [
             IconButton(onPressed: (){
              Navigator.push(context, MaterialPageRoute(builder: (context)=> DonationSliderPage() ));
             }, icon: Icon(Icons.notifications,color: AppColors.secondary,)),
                SizedBox(width: 8,),
                IconButton(onPressed: (){
                   Navigator.push(context, MaterialPageRoute(builder: (context)=> PayDetailsPage() ));
                }, icon: Icon(Icons.search,color: AppColors.secondary,)),
              ] 
              
            ),
            Padding(
              padding: const EdgeInsets.only(top: 20),
              child: Container(height: 130,width: 600,
          decoration: BoxDecoration(
            color: Colors.transparent
          ),


       child: MyWidget(),



          // old ??????????????????????????????????????????/
        
          // child: Row(
          //   mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          //   children: [
          
          //    Column(
          //      children: [
          //        InkWell(
          //         onTap: (){
          //           Navigator.push(context, MaterialPageRoute(builder: (context)=> ZakahPage()));
          //         },
          //          child: Card(
                    
          //           color: AppColors.secondary,
          //           elevation: 20,
          //           shape: RoundedRectangleBorder(
          //             borderRadius: BorderRadius.circular(10)
          //           ),
                  
          //           child: Container(height: 55,width: 55,
          //             child: Image.asset('assets/images/zakat.png')),
          //          ),
          //        ),
          //        Text('Zakat',style: TextStyle(
          //         color: AppColors.primary,fontWeight: FontWeight.w700,fontSize: 15
          //        ),)
          //      ],
          //    ),
          
          
          
          
          
          
          
          //     Column(
          //      children: [
          //        InkWell(
          //         onTap: (){
          //            Navigator.push(context, MaterialPageRoute(builder: (context)=> BeforeVolunteerPage()));
          //         },
          //          child: Card(
                    
          //           color: AppColors.secondary,
          //           elevation: 20,
          //           shape: RoundedRectangleBorder(
          //             borderRadius: BorderRadius.circular(10)
          //           ),
                  
          //           child: Container(height: 55,width: 55,
          //             child: Image.asset('assets/images/6.png')),
          //          ),
          //        ),
          //        Text('volounter',style: TextStyle(
          //         color: AppColors.primary,fontWeight: FontWeight.w700,fontSize: 15
          //        ),)
          //      ],
          //    ),
          
          
          //     Column(
          //      children: [
          //        InkWell(
          //         onTap: (){
          //           Navigator.push(context, MaterialPageRoute(builder: (context)=>PeriodicallyDonaition()));
          //         },
          //          child: Card(
                    
          //           color: AppColors.secondary,
          //           elevation: 20,
          //           shape: RoundedRectangleBorder(
          //             borderRadius: BorderRadius.circular(10)
          //           ),
                  
          //           child: Container(height: 55,width: 55,
          //             child: Image.asset('assets/images/7.png')),
          //          ),
          //        ),
          //        Text('periodecially\ndonaition',style: TextStyle(
          //         color: AppColors.primary,fontWeight: FontWeight.w700,fontSize: 15
          //        ),)
          //      ],
          //    ),
          
          
          
          
          
          //  Column(
          //      children: [
          //        InkWell(
          //         onTap: (){
          //           Navigator.push(context, MaterialPageRoute(builder: (context)=> SadakahPage()));
          //         },
          //          child: Card(
                    
          //           color: AppColors.secondary,
          //           elevation: 20,
          //           shape: RoundedRectangleBorder(
          //             borderRadius: BorderRadius.circular(10)
          //           ),
                  
          //           child: Container(height: 55,width: 55,
          //             child: Image.asset('assets/images/8.png')),
          //          ),
          //        ),
          //        Text('sadakah',style: TextStyle(
          //         color: AppColors.primary,fontWeight: FontWeight.w700,fontSize: 15
          //        ),)
          //      ],
          //    ),
          
              
          //   ],
          // ),

              ),
            ),
            SizedBox(
              height: 5,
            ),
            
            Image.asset('assets/images/22.png',height:200,width: 200,),
            SizedBox(height: 10,),
            Padding(
              padding: const EdgeInsets.only(left:10,right: 10,bottom: 10),
              child: Text('Ongoing Campaigns',style: TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.w700,
          color: AppColors.primary
              ),),
            ),
      
      
      
      
      
      SizedBox(height: 170,
        child: Padding(
      padding: const EdgeInsets.only(left: 10,right: 10),
      child: CarouselSlider(options: CarouselOptions(
         height: 190,
         enlargeCenterPage: true,
         viewportFraction: 0.6,
         autoPlay: true
      ),items: List.generate(titles.length, (index){
        return Builder(builder: (BuildContext context){
return Container(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(12)
            ),
            height:50,
                        width: 200,
                        child: Card(
                          
                          elevation: 10,
        child: Container(
          decoration: BoxDecoration(
         borderRadius: BorderRadius.circular(12),
            image: DecorationImage(image: AssetImage('assets/images/d.jpg',),fit:BoxFit.cover )
          ),
          child: Stack(
            children: [
              Positioned(
                         child: Container(
        
                           height:200,
                           width: double.infinity,
                           decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                  gradient: LinearGradient(
                    begin: Alignment.bottomCenter,
                           end: Alignment.topCenter,
                    colors: [AppColors.primary.withOpacity(0.9), Colors.transparent])
                           ),
                         ),
                       ),
        
                       Positioned(top: 120,
                         child: Padding(
                         padding: const EdgeInsets.all(8.0),
                         child: Text(titles[index],style: TextStyle(
                           color: AppColors.white,
                           fontWeight: FontWeight.w700
                         ),),
                                          ),
                       ),
        
            ],
          ),
        ),
                        ) ,
          );
        });
      }),
        
      ),
        ),
      )
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
          //  , SizedBox(
          //     height: 170,
      
          //    child: Padding(
          //      padding: const EdgeInsets.only(left:10,right: 10),
          //      child: ListView.builder(itemCount: 6,
          //      scrollDirection: Axis.horizontal,
          //       itemBuilder: (context,index){
          //       return Container(
          //         height:50,
          //         width: 200,
          //         child: Card(
          //           elevation: 20,
          //           color: AppColors.primary,
          //           shadowColor: AppColors.unselected,
          //      child: Column(
          //        crossAxisAlignment: CrossAxisAlignment.start,
          //        children: [
          //          Stack(
          //            children: [
          //              Container(
          //              height: 90,
          //              width: double.infinity,
          //              decoration: BoxDecoration(
          //                borderRadius: BorderRadius.circular(12),
          //                image: DecorationImage(image: AssetImage('assets/images/g.jpg'),fit: BoxFit.cover)
          //              ),
          //            ),
          //            Positioned(
          //              child: Container(
          //                height: 90,
          //                width: double.infinity,
          //                decoration: BoxDecoration(
          //       gradient: LinearGradient(
          //         begin: Alignment.bottomCenter,
          //                end: Alignment.topCenter,
          //         colors: [AppColors.primary.withOpacity(0.6),AppColors.white.withOpacity(0.0)])
          //                ),
          //              ),
          //            )
          //            ],
          //          ),
          //          Padding(
          //            padding: const EdgeInsets.all(8.0),
          //            child: Text(titles[index],style: TextStyle(
          //              color: AppColors.secondary,
          //              fontWeight: FontWeight.w700
          //            ),),
          //          ),
          //          Padding(
          //            padding: const EdgeInsets.only(left: 90,bottom: 3),
          //            child: ElevatedButton(
          //              onPressed: (){}, child: Text('details'),
          //              style: ElevatedButton.styleFrom(
          //                backgroundColor: AppColors.secondary,
          //                elevation: 10,
          //                shadowColor: AppColors.unselected,
          //                foregroundColor: AppColors.white,
          //                fixedSize: Size(94, 30)
          //              ),
          //              ),
          //          )
          //        ],
          //      ),
          //         ),
          //       );
          //      }),
          //    ),
          //   ),
      
            ,Padding(
              padding: const EdgeInsets.only(left:10,right: 10,top:20),
              child: Text('Emergency Cases',style: TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.w700,
          color: AppColors.primary
              ),),
            ),
      
      

















SizedBox(
              height: 200,
      
             child: Padding(
               padding: const EdgeInsets.only(left:10,right: 10),
               child: ListView.builder(itemCount: 6,
               scrollDirection: Axis.horizontal,
                itemBuilder: (context,index){
                return EmergencyCasesPage();
      
      
               }),
             ),
            ),








//old emergency


      
      // SizedBox(
      //         height: 150,
      
      //        child: Padding(
      //          padding: const EdgeInsets.only(left:10,right: 10),
      //          child: ListView.builder(itemCount: 6,
      //          scrollDirection: Axis.horizontal,
      //           itemBuilder: (context,index){
      //           return Row(
      //             children: [
      //               InkWell( onTap: (){},
      //                 child: Container(
      //                           height: 130,width: 180,
      //                          decoration: BoxDecoration(
      //                           borderRadius: BorderRadius.circular(12),
      //                           color: AppColors.white
      //                          ),
      //                          child: Card(
      //                           elevation: 10,
      //                            child: Column(
      //                             crossAxisAlignment: CrossAxisAlignment.end,
                                  
      //                             children: [
      //                               Row(
      //                                 crossAxisAlignment: CrossAxisAlignment.start,
      //                                 children: [
      //                                   Padding(
      //                                     padding: const EdgeInsets.all(8.0),
      //                                     child: Container(
      //                                       height: 40,
      //                                       width: 40,
      //                                       decoration: BoxDecoration(
      //                                         borderRadius: BorderRadius.circular(12),
      //                                         color: AppColors.secondary
                                              
      //                                       ),
      //                                       child: 
      //                                          Image.asset(
      //                                           'assets/images/n.png',),
      //                                     ),
      //                                   ),
      //                                   Column(
      //                                     crossAxisAlignment: CrossAxisAlignment.end,
      //                                     children: [
      //                                       Text('Ahmad, orphan\nfrom aleppo',style: TextStyle(
      //                                         color: AppColors.primary,fontWeight: FontWeight.w700
      //                                       ),),
      //                                       Text('case number : ',style: TextStyle(
      //                                         color: const Color.fromARGB(107, 0, 0, 0),fontWeight: FontWeight.w500
      //                                       )
      //                                       ),
      //                                       Text('22222',style: TextStyle(
      //                                         color: AppColors.secondary,fontWeight: FontWeight.w700
      //                                       )
      //                                       ),
                                            
      //                                     ],
      //                                   )
                                      
                                           
      //                                 ],
      //                               ),
      //                                Container(width: 180,
      //                                         child: Divider(color: AppColors.primary,thickness: 2,endIndent: 5,indent: 5,)),
                                                     
      //                             Padding(
      //                               padding: const EdgeInsets.only(left:10,right: 10),
      //                               child: Text('orphan',
      //                               style: TextStyle(
      //                                           color: AppColors.primary,fontWeight: FontWeight.w700
      //                                         )),
      //                             )
                                                     
      //                             ],
      //                            ),
      //                          ),
      //                         ),
      //               ),
      //                       SizedBox(width: 5,)
      //             ],
      //           );
      
      
      //          }),
      //        ),
      //       ),
      
          
       Padding(
              padding: const EdgeInsets.only(left:10,right: 10,top:20),
              child: Text('Services',style: TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.w700,
          color: AppColors.primary
              ),),
            ),
      
          SizedBox(height: 180,
            child: Padding(
              padding: const EdgeInsets.only(right: 10,left: 30),
              child: ListView(
                scrollDirection: Axis.horizontal,
                children: [
                  Column(
                    children: [
                      SizedBox(
                        width: 160,
                        height: 140,
                        child: InkWell(
                          onTap: (){
                            Navigator.push(context, MaterialPageRoute(builder: (context)=> BeforeGift() ));
                          },
                          child: Card(
                           
                            elevation: 10,
                            color: Color(0xffeaf8f9),
                            child: Column(
                              children: [
                                Image.asset('assets/images/giftt.png',height: 90,width: 90,color: AppColors.teal,),
                                SizedBox(height: 10,),
                                Text('Gift',style: TextStyle(
                                              color: AppColors.primary,fontWeight: FontWeight.w700,fontSize: 16
                                            ))
                              ],
                              
                            ),
                          ),
                        ),
                        
                      ),
                    ],
                  ),
              
              
              SizedBox(width: 30,),
      
      
      
      
      
      
      
      
              Column(
                children: [
                  SizedBox(
                    width: 160,
                    height: 140,
                    child: InkWell(
                      onTap: (){
                        Navigator.push(context, MaterialPageRoute(builder: (context)=> BeforeInkindDonaition()));
                      },
                      child: Card(
                       
                        elevation: 10,
                        color: Color(0xffeaf8f9),
                        child: Column(
                          children: [
                            Padding(
                              padding: const EdgeInsets.only(top:10),
                              child: Image.asset('assets/images/l.png',height: 80,width: 80,color: const Color.fromARGB(255, 191, 50, 118),),
                            ),SizedBox(height: 10,),
                            Text('In-kind Donations',style: TextStyle(
                                          color: AppColors.primary,fontWeight: FontWeight.w700
                                        ))
                          ],
                          
                        ),
                      ),
                    ),
                    
                  ),
                ],
              ),
              
              
                   
                ],
              ),
            ),
          )
      
      
      
          ],
          ),
        ),
       
      ),
    );
  }
}