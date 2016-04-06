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

public class transformImages extends HttpServlet {
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
      
			      
    public void init() throws ServletException {
        
        try {
            PrintStream fos = new PrintStream("trans_debug_init.txt");
            fos.println("Initialising transform servlet");
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

        String directory = imageFolder;
        while (st.ttype!=StreamTokenizer.TT_EOF) {
            String imgName = st.sval;
            st.nextToken();
            String temName = st.sval;
            st.nextToken();
            Facemorph.Template tem = new Facemorph.Template();
            fos.println("Reading template " + temName);
            tem.read(directory + temName);
            
            fos.println("Reading image " + imgName);
            ImageIcon ii = new ImageIcon(directory + imgName);
            Image img = ii.getImage();
            
            ImageTemplate it = new ImageTemplate(img, tem);
            imageMap.put(imgName, it);
            
        }
        fr.close();
        fos.println("Completed initialising transform servlet");
        fos.flush();
        fos.close();
        } catch (IOException e) {
            System.out.println(e);
            throw new ServletException(e);
        }
    }
   
	 
    public void doPost (HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
        PrintStream fos = new PrintStream("trans_debug_post.txt");
        fos.println("Doing post");
        fos.flush();
        fos.close();
    
        doGet(req,res);
    }
    
	
    public void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
        PrintStream fos = new PrintStream("trans_debug_get.txt");
        fos.println("Doing get");

        // Use a single parameter called images that holds a list of images
        int count = Integer.parseInt(req.getParameter("count"));
        
        Image[] outImg = new Image[count];
        File[] files = new File[count];
		File[] tems = new File[count];
		
		String subfolder = req.getParameter("subfolder");
		fos.println("Using output folder: " + outputFolder + subfolder);
		File uploadDir = new File(outputFolder + subfolder); 
		if (deleteTime>=0) deleteOldFiles(uploadDir);
		
        for(int i=0; i<count; i++) {
			String[] names = new String[3];
			names[0] = req.getParameter("transimage" + i);
			names[1] = req.getParameter("fromimage" + i);
			names[2] = req.getParameter("toimage" + i);
			
			double pcnt = Double.parseDouble(req.getParameter("pcnt" + i));
			
            Image[] imlist = new Image[names.length];
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
						
						ImageTemplate it2 = new ImageTemplate(img, tem);
						//imageMap.put(imgName, it2);
						
						imlist[j] = it2.img;
						tmlist[j] = it2.tem;
				   } else {
						fos.println("Loading " + names[j]);
						imlist[j] = it.img;
						tmlist[j] = it.tem;
					}
                
            }
            
			fos.println("Making Transform...");
            Facemorph.Template avTem = new Facemorph.Template();
            Image average = null;
			
			average = Transformer.transform(tmlist[0], tmlist[1], tmlist[2], avTem, imlist[0], imlist[1], imlist[2], pcnt, null, false);
			if (average==null)  fos.println("  transform null");
			
			// write out image file
            File f = File.createTempFile("img", ".jpg", uploadDir);
            //f.deleteOnExit();
            FileOutputStream imout = new FileOutputStream(f);
            files[i]=f;
            ImageToJpeg.writeJpeg(average, imout, null);
            imout.flush();
            imout.close();
		
			// write out .tem file
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
		//out.println(files[0].getName() + "," + tems[0].getName());

		String allfiles = "";
		String alltems = "";
		for (int k=0; k<count; k++) {
			if (k > 0) {
				allfiles = allfiles + ",";
				alltems = alltems + ",";
			}
			allfiles = allfiles + files[k].getName();
			alltems = alltems + tems[k].getName();
		}
		out.println(allfiles + ";" + alltems);

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
