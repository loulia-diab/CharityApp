// import 'package:flutter/material.dart';

// class OrphanSponsorshipPage extends StatefulWidget {
//   const OrphanSponsorshipPage({super.key});

//   @override
//   State<OrphanSponsorshipPage> createState() => _OrphanSponsorshipPageState();
// }

// class _OrphanSponsorshipPageState extends State<OrphanSponsorshipPage> {
//   TextEditingController amountController = TextEditingController();
//   int selectedIndex = 0; // 0 = Monthly, 1 = Yearly
//   int duration = 1; // Number of months or years

//   int get amount {
//     final text = amountController.text;
//     return int.tryParse(text) ?? 0;
//   }

//   int get total {
//     if (selectedIndex == 0) {
//       // Monthly
//       return amount * duration;
//     } else {
//       // Yearly
//       return amount * (duration * 12);
//     }
//   }

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       appBar: AppBar(
//         title: const Text('Orphan Sponsorship'),
//       ),
//       body: Padding(
//         padding: const EdgeInsets.all(16.0),
//         child: Column(
//           children: [
//             const SizedBox(height: 20),
//             TextFormField(
//               controller: amountController,
//               keyboardType: TextInputType.number,
//               decoration: const InputDecoration(
//                 labelText: "Sponsorship Amount",
//                 prefixIcon: Icon(Icons.attach_money),
//                 border: OutlineInputBorder(),
//               ),
//               onChanged: (_) => setState(() {}),
//             ),
//             const SizedBox(height: 20),

//             // Toggle for Monthly / Yearly
//             ToggleButtons(
//               isSelected: [selectedIndex == 0, selectedIndex == 1],
//               onPressed: (int index) {
//                 setState(() {
//                   selectedIndex = index;
//                   duration = 1;
//                 });
//               },
//               children: const [
//                 Padding(
//                   padding: EdgeInsets.symmetric(horizontal: 20),
//                   child: Text("Monthly"),
//                 ),
//                 Padding(
//                   padding: EdgeInsets.symmetric(horizontal: 20),
//                   child: Text("Yearly"),
//                 ),
//               ],
//             ),
//             const SizedBox(height: 20),

//             // Duration selector
//             Row(
//               mainAxisAlignment: MainAxisAlignment.spaceBetween,
//               children: [
//                 Text(selectedIndex == 0 ? "Months: $duration" : "Years: $duration"),
//                 Expanded(
//                   child: Slider(
//                     value: duration.toDouble(),
//                     min: 1,
//                     max: 24,
//                     divisions: 23,
//                     label: duration.toString(),
//                     onChanged: (value) {
//                       setState(() {
//                         duration = value.toInt();
//                       });
//                     },
//                   ),
//                 ),
//               ],
//             ),
//             const SizedBox(height: 20),

//             // Total
//             Text(
//               "Total: \$${total}",
//               style: const TextStyle(
//                 fontSize: 22,
//                 fontWeight: FontWeight.bold,
//                 color: Colors.green,
//               ),
//             ),
//             const SizedBox(height: 30),

//             ElevatedButton.icon(
//               onPressed: amount > 0 ? () {
//                 ScaffoldMessenger.of(context).showSnackBar(
//                   SnackBar(content: Text("You paid \$${total}")),
//                 );
//               } : null,
//               icon: const Icon(Icons.payment),
//               label: const Text("Pay Now"),
//               style: ElevatedButton.styleFrom(
//                 minimumSize: const Size(double.infinity, 50),
//               ),
//             )
//           ],
//         ),
//       ),
//     );
//   }
// }
import 'package:flutter/material.dart';
import 'package:percent_indicator/circular_percent_indicator.dart';

class DonationSliderPage extends StatefulWidget {
  @override
  _DonationSliderPageState createState() => _DonationSliderPageState();
}

class _DonationSliderPageState extends State<DonationSliderPage> {
  double monthlyAmount = 800;
  String donationType = 'monthly'; // 'monthly' or 'yearly'

  double get totalAmount =>
      donationType == 'monthly' ? monthlyAmount : monthlyAmount * 12;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFFCEFF5),
      appBar: AppBar(
        title: Text("BARDHYL"),
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        elevation: 0,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // Circular slider indicator
            Stack(
              alignment: Alignment.center,
              children: [
                CircularPercentIndicator(
                  radius: 110.0,
                  lineWidth: 12.0,
                  percent: (monthlyAmount - 400) / (1200 - 400),
                  center: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.star, color: Colors.amber),
                      SizedBox(height: 4),
                      Text(
                        "${monthlyAmount.toInt()}",
                        style: TextStyle(
                            fontSize: 36,
                            fontWeight: FontWeight.bold,
                            color: Colors.pink),
                      ),
                      Text("QAR",
                          style:
                              TextStyle(fontSize: 16, color: Colors.grey[700])),
                      Text("Monthly amount",
                          style:
                              TextStyle(fontSize: 12, color: Colors.grey[600])),
                    ],
                  ),
                  progressColor: Colors.pink,
                  backgroundColor: Colors.grey[200]!,
                  circularStrokeCap: CircularStrokeCap.round,
                ),
              ],
            ),

            // Slider (Horizontal under the circle)
            Slider(
              value: monthlyAmount,
              min: 400,
              max: 1200,
              divisions: 8,
              label: monthlyAmount.toInt().toString(),
              activeColor: Colors.pink,
              inactiveColor: Colors.pink.shade100,
              onChanged: (value) {
                setState(() {
                  monthlyAmount = value;
                });
              },
            ),
            Text(
              "Please consider increasing your donation for more impact",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.black54),
            ),
            SizedBox(height: 20),

            // Radio buttons for Monthly / Yearly
            Column(
              children: [
                RadioListTile(
                  value: 'monthly',
                  groupValue: donationType,
                  onChanged: (val) => setState(() => donationType = val!),
                  title: Text("${monthlyAmount.toInt()} QAR"),
                  subtitle:
                      Text("Monthly donation –– Automatically deducted"),
                ),
                RadioListTile(
                  value: 'yearly',
                  groupValue: donationType,
                  onChanged: (val) => setState(() => donationType = val!),
                  title: Text(
                      "${(monthlyAmount * 12).toInt()} QAR (${monthlyAmount.toInt()} × 12 Month)"),
                ),
              ],
            ),

            Spacer(),

            // Total + Donate Button
            Container(
              padding: EdgeInsets.all(16),
              decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [BoxShadow(blurRadius: 6, color: Colors.black12)]),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("Total Donation",
                            style: TextStyle(
                                fontSize: 16, fontWeight: FontWeight.bold)),
                        Text(
                          "${totalAmount.toInt()} QAR",
                          style: TextStyle(
                              fontSize: 18,
                              color: Colors.pink,
                              fontWeight: FontWeight.bold),
                        ),
                      ]),
                  ElevatedButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                          content: Text(
                              "You donated ${totalAmount.toInt()} QAR")));
                    },
                    icon: Icon(Icons.payment),
                    label: Text("Donate"),
                    style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.pink,
                        shape: StadiumBorder(),
                        padding:
                            EdgeInsets.symmetric(horizontal: 24, vertical: 14)),
                  )
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
