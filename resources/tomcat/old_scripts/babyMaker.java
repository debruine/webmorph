package PsychoServlet;

/*
 * Created on Apr 12, 2006 at 10:24:10 AM.
 */

import java.io.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import javax.swing.*;
import java.awt.*;
import java.util.Enumeration;
import org.apache.commons.fileupload.*;
import org.apache.commons.fileupload.servlet.ServletFileUpload;
import org.apache.commons.fileupload.FileUpload;
import org.apache.commons.fileupload.disk.DiskFileItemFactory;
import Facemorph.*;

public class babyMaker extends HttpServlet {
    class ImageTemplate {
        public Image img;
        public Template tem;
        public ImageTemplate(Image i, Template t) {
            img = i;
            tem = t;
        }
    }
    
    TreeMap<String, ImageTemplate> imageMap = new TreeMap<String,ImageTemplate>();
    int deleteTime = 0;
	String mydirectory = null;
    String imageFolder = null;
    String configFile = null;
    String outputFolder = null;

	Image adultImg = null;
	Image babyImg = null;
	Facemorph.Template adultTem = new Facemorph.Template();
	Facemorph.Template babyTem = new Facemorph.Template();
            
    public void init() throws ServletException {
        
        try {
            PrintStream fos = new PrintStream("baby_debug_init.txt");
            fos.println("Initialising babymaker servlet");
            // Preload the images
            mydirectory = this.getInitParameter("mydirectory");
            configFile = this.getInitParameter("configFile");
            imageFolder = this.getInitParameter("imageFolder");
            outputFolder = this.getInitParameter("outputFolder");
            deleteTime = Integer.parseInt(getInitParameter("deleteTime"));
			fos.println("mydirectory = " + mydirectory);
            fos.println("configFile = " + configFile);
            fos.println("imageFolder = " + imageFolder);
            fos.println("outputFolder = " + outputFolder);
            fos.println("deleteTime = " + deleteTime);
            
            
        FileReader fr = new FileReader(configFile);
        StreamTokenizer st = new StreamTokenizer(fr);
        st.quoteChar((int)'\"');
        st.wordChars((int)'_', (int)'_');
        st.wordChars((int)'\\', (int)'\\');
        st.nextToken();
		
		fos.println("  Reading template from " + imageFolder + "androgyn.tem");
		adultTem.read(imageFolder + "androgyn.tem");
		
		fos.println("  Reading image from " + imageFolder + "androgyn.jpg");
		ImageIcon iia = new ImageIcon(imageFolder + "androgyn.jpg");
		adultImg = iia.getImage();
		
		fos.println("  Reading template from " + imageFolder + "baby.tem");
		babyTem.read(imageFolder + "baby.tem");
		
		fos.println("  Reading image from " + imageFolder + "baby.jpg");
		ImageIcon iib = new ImageIcon(imageFolder + "baby.jpg");
		babyImg = iib.getImage();
			
        fr.close();
        fos.println("Completed initialising babymaker servlet");
        fos.flush();
        fos.close();
        } catch (IOException e) {
            System.out.println(e);
            throw new ServletException(e);
        }
    }
    
    public void doPost (HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
        PrintStream fos = new PrintStream("baby_debug_post.txt");
        fos.println("Doing post");
        fos.flush();
        fos.close();
    
        doGet(req,res);
    }
    
    
    
    public void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
        PrintStream fos = new PrintStream("baby_debug_get.txt");
        fos.println("Doing get");

		//int parentWidth = Integer.parseInt(req.getParameter("w"));
		//int parentHeight = Integer.parseInt(req.getParameter("h"));
        int count = Integer.parseInt(req.getParameter("count"));
        
        Image[] outImg = new Image[count];
        File[] files = new File[count];
		File[] tems = new File[count];
		
		String subfolder = req.getParameter("subfolder");
		fos.println("Using output folder: " + outputFolder + subfolder);
		File uploadDir = new File(outputFolder + subfolder); 
		if (deleteTime>=0) deleteOldFiles(uploadDir);
		
        for(int i=0; i<count; i++) {
			String[] names = new String[2];
			names[0] = req.getParameter("momimage" + i);
			names[1] = req.getParameter("dadimage" + i);
			
			double pcnt = Double.parseDouble(req.getParameter("pcnt" + i));
			
            Image[] imlist = new Image[names.length];
			int[] widthlist = new int[names.length];
			int[] heightlist = new int[names.length];
			
            Facemorph.Template[] tmlist = new Facemorph.Template[names.length];
              fos.println("names[" + i + "].length = " + names.length);
			
            for (int j=0; j<names.length; j++) {
                // Should check names are in map
                ImageTemplate it = imageMap.get(names[j]);
                   if (it==null) {
						fos.println("Loading NEW " + names[j]);
						// load img and tem
						String imgName = names[j] + ".jpg";
						String temName = names[j] + ".tem";
						
						fos.println("  Reading template from " + mydirectory + temName);
						Facemorph.Template tem = new Facemorph.Template();
						tem.read(mydirectory + temName);
						
						fos.println("  Reading image from " + mydirectory + imgName);
						ImageIcon ii = new ImageIcon(mydirectory + imgName);
						Image img = ii.getImage();
						
						widthlist[j] = img.getWidth(null);
						heightlist[j] = img.getHeight(null);
						
						imlist[j] = img;
						tmlist[j] = tem;
				   } else {
						fos.println("Loading " + names[j]);
						imlist[j] = it.img;
						tmlist[j] = it.tem;
					}
                
            }
			
			// get the maximum parent image width and height
			Arrays.sort(widthlist);
			Arrays.sort(heightlist);
			int parentWidth = widthlist[1];
			int parentHeight = heightlist[1];
			
			// make the mom+dad average
			fos.println("Making Average...");
            Facemorph.Template parentTem = new Facemorph.Template();
            Image parentAv = null;
            parentAv = Transformer.averageImages(imlist, tmlist, parentTem, parentWidth, parentHeight, null, false);
            if (parentAv==null)  fos.println("  average null");
			
            // transform the face to a baby
			fos.println("Making baby...");
            Facemorph.Template avTem = new Facemorph.Template();
            Image baby = null;
			baby = Transformer.transform(parentTem, adultTem, babyTem, avTem, parentAv, adultImg, babyImg, pcnt, null, false);
			if (baby==null)  fos.println("  baby image null");
			
			fos.println("Writing image file...");
            File f = File.createTempFile("img", ".jpg", uploadDir);
            //f.deleteOnExit();
            FileOutputStream imout = new FileOutputStream(f);
            files[i]=f;
            ImageToJpeg.writeJpeg(baby, imout, null);
            imout.flush();
            imout.close();
			
			// write out .tem file
			fos.println("Writing tem file");
			File t = File.createTempFile("img", ".tem", uploadDir);
			FileOutputStream temout = new FileOutputStream(t);
			tems[i]=t;
			PrintStream tem_ps = new PrintStream(new BufferedOutputStream(temout), true);
			avTem.write(tem_ps);
			tem_ps.flush();
			tem_ps.close();
        }
        
		// return image and template names
        String outputURL = req.getParameter("url");
		fos.println("outputURL = " + outputURL);

        //New print writer to write output to
		res.setContentType("text/html");
		PrintWriter out = res.getWriter();
		out.println(files[0].getName() + "," + tems[0].getName());
 
        fos.flush();
        fos.close();
   
        out.flush();
        out.close();
    }

    public void deleteOldFiles(File directory) {
      File[] files = directory.listFiles(new FilenameFilter() {
          public boolean accept(File dir, String name)  {
              if (name.endsWith(".jpg")) return true;
			  if (name.endsWith(".tem")) return true;
              return false;
          }
      });
      long current = System.currentTimeMillis();
      long minute = 1000*deleteTime;
      for (int i=0; i<files.length; i++) {
          if (files[i].lastModified()<current-minute) files[i].delete();
      }
  }
  
}
